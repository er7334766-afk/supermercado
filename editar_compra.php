<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('compras', 'editar');

$idCompra = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$compra = null;
$detalleCompra = [];
$proveedores = [];
$productos = [];
$errores = [];
$mensajeExito = '';

try {
    $stmt = $conexion->prepare('SELECT id_proveedor, nombre_empresa FROM proveedores WHERE estado = 1 ORDER BY nombre_empresa ASC');
    $stmt->execute();
    $proveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $proveedores = [];
}

try {
    $stmt = $conexion->prepare('SELECT id_producto, codigo_barras, nombre, precio_compra FROM productos WHERE estado = 1 ORDER BY nombre ASC');
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCompra = (int)($_POST['id_compra'] ?? 0);
    $fkProveedor = (int)($_POST['fk_proveedor'] ?? 0);
    $numeroFactura = trim((string)($_POST['numero_factura'] ?? ''));
    $fechaCompra = trim((string)($_POST['fecha_compra'] ?? date('Y-m-d H:i:s')));
    $estado = trim((string)($_POST['estado'] ?? 'Registrada'));
    $productosPost = $_POST['producto'] ?? [];
    $preciosPost = $_POST['precio_compra'] ?? [];
    $cantidadesPost = $_POST['cantidad'] ?? [];

    if ($numeroFactura === '') {
        $numeroFactura = 'COMP-' . date('YmdHis');
    }

    $detalle = [];
    $subtotal = 0.0;
    for ($i = 0; $i < count($productosPost); $i++) {
        $productoId = (int)($productosPost[$i] ?? 0);
        $precio = (float)($preciosPost[$i] ?? 0);
        $cantidad = (int)($cantidadesPost[$i] ?? 0);
        if ($productoId <= 0 || $cantidad <= 0) {
            continue;
        }
        $lineaSubtotal = round($precio * $cantidad, 2);
        $subtotal += $lineaSubtotal;
        $detalle[] = [
            'producto_id' => $productoId,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'linea_subtotal' => $lineaSubtotal,
        ];
    }

    if (empty($detalle)) {
        $errores[] = 'Debe incluir al menos un producto en la compra.';
    } else {
        $impuesto = round($subtotal * 0.15, 2);
        $total = round($subtotal + $impuesto, 2);

        try {
            $conexion->beginTransaction();

            $stmt = $conexion->prepare('UPDATE compras SET fk_proveedor = ?, numero_factura = ?, fecha_compra = ?, subtotal = ?, impuesto = ?, total = ?, estado = ? WHERE id_compra = ?');
            $stmt->execute([
                $fkProveedor > 0 ? $fkProveedor : null,
                $numeroFactura,
                $fechaCompra,
                $subtotal,
                $impuesto,
                $total,
                $estado,
                $idCompra,
            ]);

            $stmtDelete = $conexion->prepare('DELETE FROM detalle_compras WHERE fk_compra = ?');
            $stmtDelete->execute([$idCompra]);

            $stmtDetalle = $conexion->prepare('INSERT INTO detalle_compras (fk_compra, fk_producto, cantidad, precio_compra, subtotal) VALUES (?, ?, ?, ?, ?)');
            foreach ($detalle as $item) {
                $stmtDetalle->execute([
                    $idCompra,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio'],
                    $item['linea_subtotal'],
                ]);
            }

            $conexion->commit();
            $mensajeExito = 'Compra actualizada correctamente.';
            header('Location: compras.php?success=1');
            exit;
        } catch (PDOException $e) {
            $conexion->rollBack();
            $errores[] = 'No se pudo actualizar la compra.';
        }
    }
}

if ($idCompra > 0) {
    try {
        $stmt = $conexion->prepare('SELECT id_compra, fk_proveedor, numero_factura, fecha_compra, subtotal, impuesto, total, estado FROM compras WHERE id_compra = ? LIMIT 1');
        $stmt->execute([$idCompra]);
        $compra = $stmt->fetch();

        $stmtDetalle = $conexion->prepare('SELECT fk_producto, cantidad, precio_compra, subtotal FROM detalle_compras WHERE fk_compra = ? ORDER BY id_detalle_compra ASC');
        $stmtDetalle->execute([$idCompra]);
        $detalleCompra = $stmtDetalle->fetchAll();
    } catch (PDOException $e) {
        $errores[] = 'No se pudo cargar la compra solicitada.';
    }
}

if (!$compra) {
    $errores[] = 'La compra solicitada no existe.';
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Compra</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>
    <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center header-title">
            <h2>Editar Compra</h2>
            <a href="compras.php" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (!empty($errores)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errores[0]); ?></div>
                <?php endif; ?>
                <?php if ($compra) : ?>
                    <form action="editar_compra.php?id=<?php echo intval($idCompra); ?>" method="POST">
                        <input type="hidden" name="id_compra" value="<?php echo intval($compra['id_compra']); ?>">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" name="fk_proveedor" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($proveedores as $proveedor) : ?>
                                        <option value="<?php echo intval($proveedor['id_proveedor']); ?>" <?php echo ((int)($compra['fk_proveedor'] ?? 0) === (int)$proveedor['id_proveedor']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número de Factura</label>
                                <input type="text" class="form-control" name="numero_factura" value="<?php echo htmlspecialchars($compra['numero_factura']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="datetime-local" class="form-control" name="fecha_compra" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($compra['fecha_compra']))); ?>">
                            </div>
                        </div>
                        <hr>
                        <h5>Productos de la Compra</h5>
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Compra</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detalleCompra)) : ?>
                                    <?php foreach ($detalleCompra as $index => $item) : ?>
                                        <tr>
                                            <td>
                                                <select class="form-select" name="producto[]">
                                                    <option value="">Seleccione producto...</option>
                                                    <?php foreach ($productos as $producto) : ?>
                                                        <option value="<?php echo intval($producto['id_producto']); ?>" <?php echo ((int)$item['fk_producto'] === (int)$producto['id_producto']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($producto['nombre']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" class="form-control" name="precio_compra[]" value="<?php echo number_format((float)$item['precio_compra'], 2, '.', ''); ?>"></td>
                                            <td><input type="number" class="form-control" name="cantidad[]" value="<?php echo intval($item['cantidad']); ?>"></td>
                                            <td><input type="number" step="0.01" class="form-control" value="<?php echo number_format((float)$item['subtotal'], 2, '.', ''); ?>" readonly></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td>
                                            <select class="form-select" name="producto[]">
                                                <option value="">Seleccione producto...</option>
                                                <?php foreach ($productos as $producto) : ?>
                                                    <option value="<?php echo intval($producto['id_producto']); ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" class="form-control" name="precio_compra[]" value="0.00"></td>
                                        <td><input type="number" class="form-control" name="cantidad[]" value="1"></td>
                                        <td><input type="number" step="0.01" class="form-control" value="0.00" readonly></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Subtotal</label>
                                <input type="number" step="0.01" class="form-control" name="subtotal" value="<?php echo number_format((float)($compra['subtotal'] ?? 0), 2, '.', ''); ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Impuesto</label>
                                <input type="number" step="0.01" class="form-control" name="impuesto" value="<?php echo number_format((float)($compra['impuesto'] ?? 0), 2, '.', ''); ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Total</label>
                                <input type="number" step="0.01" class="form-control" name="total" value="<?php echo number_format((float)($compra['total'] ?? 0), 2, '.', ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="Registrada" <?php echo (($compra['estado'] ?? '') === 'Registrada') ? 'selected' : ''; ?>>Registrada</option>
                                <option value="Pendiente" <?php echo (($compra['estado'] ?? '') === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Anulada" <?php echo (($compra['estado'] ?? '') === 'Anulada') ? 'selected' : ''; ?>>Anulada</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Actualizar Compra</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>