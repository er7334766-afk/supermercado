<?php
session_start();

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';
require_once 'config/auditoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: devoluciones.php');
    exit;
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('devoluciones', 'crear');

$fk_usuario = (int)$_SESSION['usuario_id'];
$fk_venta = isset($_POST['fk_venta']) ? (int)$_POST['fk_venta'] : 0;
$motivo = trim((string)($_POST['motivo'] ?? ''));

if ($fk_venta <= 0 || $motivo === '') {
    header('Location: nueva_devolucion.php?error=' . urlencode('Datos incompletos'));
    exit;
}

try {
    // Usar transacción para insertar devolución y actualizar caja
    $conexion->beginTransaction();

    // Obtener información de la venta para conocer monto y apertura
    $stmtV = $conexion->prepare('SELECT id_venta, total, fk_apertura, numero_factura FROM ventas WHERE id_venta = ? LIMIT 1');
    $stmtV->execute([$fk_venta]);
    $venta = $stmtV->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        $conexion->rollBack();
        $msg = 'Venta no encontrada.';
        header('Location: nueva_devolucion.php?error=' . urlencode($msg));
        exit;
    }

    // Por defecto, total_devuelto = 0; si no se envían items, se usará el total de la venta
    $total_devuelto = 0.0;
    $fk_apertura_venta = isset($venta['fk_apertura']) ? (int)$venta['fk_apertura'] : null;
    $numero_factura = $venta['numero_factura'] ?? null;

    // Insertar devolución incluyendo total_devuelto
    $stmt = $conexion->prepare('INSERT INTO devoluciones (fk_venta, fk_usuario, motivo, fecha_devolucion, total_devuelto, estado) VALUES (?, ?, ?, NOW(), ?, ?)');
    $estado = 'Registrada';
    $ok = $stmt->execute([$fk_venta, $fk_usuario, $motivo, $total_devuelto, $estado]);

    if (!$ok) {
        $err = $stmt->errorInfo();
        $msg = 'No se insertó la devolución. SQLSTATE=' . ($err[0] ?? '') . ' - ' . ($err[2] ?? 'Sin mensaje de error');
        @file_put_contents(__DIR__ . '/devoluciones_errors.log', date('c') . " - ERROR INSERT devoluciones: " . $msg . " - fk_venta={$fk_venta} motivo=" . substr($motivo,0,200) . "\n", FILE_APPEND);
        $conexion->rollBack();
        header('Location: nueva_devolucion.php?error=' . urlencode($msg));
        exit;
    }

    $id_devolucion = (int)$conexion->lastInsertId();

        // Procesar detalle de devolución si el formulario envía productos y cantidades
        $productos_return = $_POST['producto'] ?? [];
        $cantidades_return = $_POST['cantidad'] ?? [];

        if (!empty($productos_return) && is_array($productos_return)) {
            // Obtener precios originales para la venta (mapa producto -> precio_unitario)
            $stmtDetalleVenta = $conexion->prepare('SELECT fk_producto, precio_unitario FROM detalle_ventas WHERE fk_venta = ?');
            $stmtDetalleVenta->execute([$fk_venta]);
            $detalleVentaRows = $stmtDetalleVenta->fetchAll(PDO::FETCH_ASSOC);
            $precioMap = [];
            foreach ($detalleVentaRows as $r) {
                $precioMap[(int)$r['fk_producto']] = (float)$r['precio_unitario'];
            }

            $stmtInsertDetalleDev = $conexion->prepare('INSERT INTO detalle_devoluciones (fk_devolucion, fk_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)');
            $stmtUpdateProducto = $conexion->prepare('UPDATE productos SET existencia = existencia + ? WHERE id_producto = ?');
            $stmtMovInv = $conexion->prepare('INSERT INTO movimientos_inventario (fk_producto, fk_usuario, tipo_movimiento, cantidad, existencia_anterior, existencia_nueva, referencia, observacion, fecha_movimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');

            for ($i = 0; $i < count($productos_return); $i++) {
                $pid = (int)$productos_return[$i];
                $qty = (int)$cantidades_return[$i];
                if ($qty <= 0) continue;

                $precio_unit = isset($precioMap[$pid]) ? $precioMap[$pid] : 0.0;
                $subtotal_line = round($precio_unit * $qty, 2);

                // Insertar detalle_devoluciones
                try {
                    $stmtInsertDetalleDev->execute([$id_devolucion, $pid, $qty, $precio_unit, $subtotal_line]);
                } catch (Throwable $e) {
                    @file_put_contents(__DIR__ . '/devoluciones_errors.log', date('c') . " - ERROR INSERT detalle_devoluciones: " . $e->getMessage() . "\n", FILE_APPEND);
                    $conexion->rollBack();
                    header('Location: nueva_devolucion.php?error=' . urlencode('Error guardando detalle de devolución: ' . $e->getMessage()));
                    exit;
                }

                // Actualizar existencia del producto (sumar lo devuelto)
                // Obtener existencia anterior para el movimiento
                $stmtExist = $conexion->prepare('SELECT existencia FROM productos WHERE id_producto = ? LIMIT 1');
                $stmtExist->execute([$pid]);
                $rowExist = $stmtExist->fetch(PDO::FETCH_ASSOC);
                $exist_anterior = $rowExist ? (int)$rowExist['existencia'] : 0;
                $exist_nueva = $exist_anterior + $qty;

                $stmtUpdateProducto->execute([$qty, $pid]);

                // Registrar movimiento de inventario por devolución
                try {
                    $stmtMovInv->execute([
                        $pid,
                        $fk_usuario,
                        'Devolucion',
                        $qty,
                        $exist_anterior,
                        $exist_nueva,
                        $numero_factura,
                        'Entrada por devolución #' . $id_devolucion
                    ]);
                } catch (Throwable $e) {
                    @file_put_contents(__DIR__ . '/devoluciones_errors.log', date('c') . " - ERROR movimientos_inventario devolucion: " . $e->getMessage() . "\n", FILE_APPEND);
                    $conexion->rollBack();
                    header('Location: nueva_devolucion.php?error=' . urlencode('Error registrando movimiento de inventario: ' . $e->getMessage()));
                    exit;
                }

                $total_devuelto += $subtotal_line;
            }

            // Actualizar total_devuelto en la fila de devoluciones
            $stmtUpdTotal = $conexion->prepare('UPDATE devoluciones SET total_devuelto = ? WHERE id_devolucion = ?');
            $stmtUpdTotal->execute([$total_devuelto, $id_devolucion]);
        } else {
            // Si no se envió detalle, asumimos devolución total de la venta
            $total_devuelto = (float)($venta['total'] ?? 0);
            $stmtUpdTotal = $conexion->prepare('UPDATE devoluciones SET total_devuelto = ? WHERE id_devolucion = ?');
            $stmtUpdTotal->execute([$total_devuelto, $id_devolucion]);
        }

    // Si hay una apertura asociada a la venta, restar el monto devuelto y registrar movimiento
    if ($fk_apertura_venta) {
        try {
            $stmtUpdateApertura = $conexion->prepare('UPDATE aperturas_caja SET monto_contado = COALESCE(monto_contado,0) - ? WHERE id_apertura = ?');
            $stmtUpdateApertura->execute([$total_devuelto, $fk_apertura_venta]);

            $stmtMovCaja = $conexion->prepare('INSERT INTO movimientos_caja (fk_apertura, fk_usuario, tipo, concepto, monto, referencia, fecha_movimiento, observacion) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)');
            $stmtMovCaja->execute([
                $fk_apertura_venta,
                $fk_usuario,
                'Egreso',
                'Devolución',
                $total_devuelto,
                $numero_factura,
                'Salida por devolución #' . $id_devolucion
            ]);
        } catch (Throwable $e) {
            // Log el error pero continuar si no queremos bloquear al usuario
            @file_put_contents(__DIR__ . '/devoluciones_errors.log', date('c') . " - ERROR actualizar caja/devolucion: " . $e->getMessage() . "\n", FILE_APPEND);
            $conexion->rollBack();
            header('Location: nueva_devolucion.php?error=' . urlencode('Error al actualizar la caja: ' . $e->getMessage()));
            exit;
        }
    }

    $conexion->commit();

    // Registrar auditoría
    try {
        $detalle = 'Devolución #' . $id_devolucion . ', venta: ' . $fk_venta . ', monto devuelto: L. ' . number_format($total_devuelto,2,'.',',') . ', motivo: ' . $motivo;
        registrarAuditoria($conexion, $fk_usuario, 'Crear devolución', $detalle, 'ventas', 'devoluciones', $id_devolucion);
    } catch (Throwable $e) {
        error_log('Error auditoría devolucion: ' . $e->getMessage());
    }

    header('Location: devoluciones.php?success=1');
    exit;
} catch (PDOException $e) {
    @file_put_contents(__DIR__ . '/devoluciones_errors.log', date('c') . " - PDOException: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Location: nueva_devolucion.php?error=' . urlencode($e->getMessage()));
    exit;
}
