<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT id_cliente, nombre, apellido FROM clientes WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
    $error = 'No se pudieron cargar los clientes: ' . $e->getMessage();
}

try {
    $stmt = $conexion->prepare("SELECT id_producto, codigo_barras, nombre, precio_venta FROM productos WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
    $error = 'No se pudieron cargar los productos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nueva Venta</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>
    <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center header-title">
            <h2>Nueva Venta</h2>
            <a href="ventas.php" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form action="guardar_venta.php" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" name="fk_cliente">
                                <option value="">Consumidor Final</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo intval($cliente['id_cliente']); ?>"><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Factura</label>
                            <input type="text" class="form-control" name="numero_factura" value="FAC-<?php echo date('YmdHis'); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="datetime-local" class="form-control" name="fecha_venta" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>
                    <hr>
                    <h5>Productos</h5>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select" name="producto[]">
                                        <option value="">Seleccione producto...</option>
                                        <?php foreach ($productos as $producto) : ?>
                                            <option value="<?php echo intval($producto['id_producto']); ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control" name="precio[]" step="0.01" value="0.00"></td>
                                <td><input type="number" class="form-control" name="cantidad[]" value="1"></td>
                                <td><input type="number" class="form-control" name="subtotal[]" step="0.01" value="0.00" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago">
                                <option>Efectivo</option>
                                <option>Tarjeta</option>
                                <option>Transferencia</option>
                                <option>Pago móvil</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto Recibido</label>
                            <input type="number" class="form-control" step="0.01" name="monto_recibido">
                        </div>
                    </div>
                    <hr>
                    <div class="row text-end">
                        <div class="col-md-12">
                            <h5>Subtotal: L. 0.00</h5>
                            <h5>Descuento: L. 0.00</h5>
                            <h5>Impuesto: L. 0.00</h5>
                            <h4 class="text-success">Total: L. 0.00</h4>
                            <h5>Cambio: L. 0.00</h5>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button class="btn btn-success">Guardar Venta</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>