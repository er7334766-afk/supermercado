<?php

session_start();

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';
require_once 'config/auditoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ventas.php');
    exit;
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('ventas', 'crear');

$fk_usuario = (int) $_SESSION['usuario_id'];

$fk_cliente = !empty($_POST['fk_cliente'])
    ? (int) $_POST['fk_cliente']
    : null;

$numero_factura = trim($_POST['numero_factura'] ?? '');

$fecha_venta = trim($_POST['fecha_venta'] ?? '');

if ($fecha_venta !== '') {
    $fecha_venta = str_replace('T', ' ', $fecha_venta);

    if (strlen($fecha_venta) === 16) {
        $fecha_venta .= ':00';
    }
} else {
    $fecha_venta = date('Y-m-d H:i:s');
}

$metodo_pago = trim($_POST['metodo_pago'] ?? 'Efectivo');
$monto_recibido = (float)($_POST['monto_recibido'] ?? 0);
$descuento = (float)($_POST['descuento'] ?? 0);

if ($numero_factura === '') {
    $numero_factura = 'FAC-' . date('YmdHis');
}


try {

    /* =============================
       BUSCAR CAJA ABIERTA / USAR FK_APERTURA ENVIADO
       ============================= */

    if (!empty($_POST['fk_apertura'])) {
        $fk_apertura = (int)$_POST['fk_apertura'];

        // Validar que la apertura exista y esté abierta
        $stmtCheck = $conexion->prepare("SELECT id_apertura FROM aperturas_caja WHERE id_apertura = ? AND estado = 'Abierta' LIMIT 1");
        $stmtCheck->execute([$fk_apertura]);
        $found = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$found) {
            throw new Exception('La apertura seleccionada no es válida o está cerrada.');
        }
    } else {
        $stmtApertura = $conexion->prepare("SELECT id_apertura FROM aperturas_caja WHERE fk_usuario = ? AND estado = 'Abierta' ORDER BY id_apertura DESC LIMIT 1");
        $stmtApertura->execute([$fk_usuario]);
        $apertura = $stmtApertura->fetch(PDO::FETCH_ASSOC);

        if (!$apertura) {
            throw new Exception('Debes tener una caja abierta antes de registrar una venta.');
        }

        $fk_apertura = (int)$apertura['id_apertura'];
    }


    /* =============================
       OBTENER PRODUCTOS DEL CARRITO
       ============================= */

    $stmtCart = $conexion->prepare("SELECT c.fk_producto, c.cantidad, p.nombre, p.precio_venta, p.existencia FROM cart_items c INNER JOIN productos p ON p.id_producto = c.fk_producto WHERE c.fk_usuario = ?");

    $stmtCart->execute([$fk_usuario]);

    $productos = $stmtCart->fetchAll(PDO::FETCH_ASSOC);

    if (empty($productos)) {
        throw new Exception('El carrito está vacío.');
    }


    /* =============================
       CALCULAR TOTALES
       ============================= */

    $subtotal = 0;

    foreach ($productos as $producto) {
        $cantidad = (int)$producto['cantidad'];
        $precio = (float)$producto['precio_venta'];
        $existencia = (int)$producto['existencia'];

        if ($cantidad <= 0) {
            throw new Exception('La cantidad de los productos debe ser mayor que cero.');
        }

        // Si no hay existencia disponible, bloquear la venta
        if ($existencia <= 0) {
            throw new Exception('No hay existencia del producto: ' . $producto['nombre']);
        }

        // Permitir que la venta reduzca el stock hasta el umbral mínimo; solo bloquear si se solicita más que lo disponible
        if ($cantidad > $existencia) {
            throw new Exception('No hay suficiente existencia de: ' . $producto['nombre']);
        }

        $subtotal += $precio * $cantidad;
    }

    $subtotal = round($subtotal, 2);

    $impuesto = round($subtotal * 0.15, 2);

    $total = round($subtotal - $descuento + $impuesto, 2);

    $cambio = 0;

    if ($metodo_pago === 'Efectivo') {

        if ($monto_recibido < $total) {
            throw new Exception('El monto recibido es menor que el total de la venta.');
        }

        $cambio = round($monto_recibido - $total, 2);
    }


    /* =============================
       INICIAR TRANSACCIÓN
       ============================= */

    $conexion->beginTransaction();


    /* =============================
       GUARDAR VENTA
       ============================= */
    $stmtVenta = $conexion->prepare("INSERT INTO ventas (fk_cliente, fk_usuario, fk_apertura, numero_factura, fecha_venta, subtotal, descuento, impuesto, total, metodo_pago, monto_recibido, cambio, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmtVenta->execute([
        $fk_cliente,
        $fk_usuario,
        $fk_apertura,
        $numero_factura,
        $fecha_venta,
        $subtotal,
        $descuento,
        $impuesto,
        $total,
        $metodo_pago,
        $monto_recibido,
        $cambio,
        'Completada'
    ]);

    $id_venta = (int)$conexion->lastInsertId();


    /* =============================
       PREPARAR CONSULTAS
       ============================= */

    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_ventas (fk_venta, fk_producto, cantidad, precio_unitario, descuento, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

    $stmtActualizarStock = $conexion->prepare("UPDATE productos SET existencia = ? WHERE id_producto = ?");

    $stmtMovimiento = $conexion->prepare("INSERT INTO movimientos_inventario (fk_producto, fk_usuario, tipo_movimiento, cantidad, existencia_anterior, existencia_nueva, referencia, observacion, fecha_movimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");


    /* =============================
       GUARDAR PRODUCTOS
       ============================= */

    foreach ($productos as $producto) {

        $fk_producto = (int)$producto['fk_producto'];

        $cantidad = (int)$producto['cantidad'];

        $precio = (float)$producto['precio_venta'];

        $existencia_anterior = (int)$producto['existencia'];

        $existencia_nueva = $existencia_anterior - $cantidad;

        $subtotal_linea = round($precio * $cantidad, 2);


        /* Detalle de venta */

        $stmtDetalle->execute([
            $id_venta,
            $fk_producto,
            $cantidad,
            $precio,
            0,
            $subtotal_linea
        ]);


        /* Actualizar existencia */

        $stmtActualizarStock->execute([
            $existencia_nueva,
            $fk_producto
        ]);


        /* Movimiento inventario */

        $stmtMovimiento->execute([
            $fk_producto,
            $fk_usuario,
            'Venta',
            $cantidad,
            $existencia_anterior,
            $existencia_nueva,
            $numero_factura,
            'Salida de producto por venta'
        ]);
    }


    /* =============================
       VACIAR CARRITO
       ============================= */

    $stmtVaciar = $conexion->prepare("DELETE FROM cart_items WHERE fk_usuario = ?");

    $stmtVaciar->execute([$fk_usuario]);


    /* =============================
       FINALIZAR: actualizar apertura y registrar movimiento de caja
       ============================= */

    try {
        $stmtUpdateApertura = $conexion->prepare('UPDATE aperturas_caja SET monto_contado = COALESCE(monto_contado,0) + ? WHERE id_apertura = ?');
        $stmtUpdateApertura->execute([$total, $fk_apertura]);

        $stmtMovCaja = $conexion->prepare('INSERT INTO movimientos_caja (fk_apertura, fk_usuario, tipo, concepto, monto, referencia, fecha_movimiento, observacion) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmtMovCaja->execute([
            $fk_apertura,
            $fk_usuario,
            'Ingreso',
            'Venta',
            $total,
            $numero_factura,
            'Ingreso por venta'
        ]);
    } catch (Throwable $e) {
        error_log('Error actualizando caja tras venta: ' . $e->getMessage());
    }

    $conexion->commit();

    // Registrar auditoría
    try {
        $detalle = 'Venta #' . $id_venta . ', total: L. ' . number_format((float)$total, 2, '.', ',') . ', cliente: ' . ($fk_cliente ?? 'N/A');
        registrarAuditoria($conexion, $fk_usuario, 'Crear venta', $detalle);
    } catch (Throwable $e) {
        // no bloquear la respuesta principal
    }

    header('Location: ventas.php?success=1');
    exit;


} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    header('Location: nueva_venta.php?error=' . urlencode($e->getMessage()));

    exit;
}
