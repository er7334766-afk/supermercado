<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('compras', 'crear');

try {
    $stmt = $conexion->prepare("SELECT id_proveedor, nombre_empresa FROM proveedores WHERE estado = 1 ORDER BY nombre_empresa ASC");
    $stmt->execute();
    $proveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $proveedores = [];
    $error = 'No se pudieron cargar los proveedores: ' . $e->getMessage();
}

try {
    $stmt = $conexion->prepare("SELECT id_producto, codigo_barras, nombre, precio_compra FROM productos WHERE estado = 1 ORDER BY nombre ASC");
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
        <title>Nueva Compra</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
    </head>
    <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center header-title">
            <h2>Nueva Compra</h2>
            <a href="compras.php" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form action="guardar_compra.php" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" name="fk_proveedor" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($proveedores as $proveedor) : ?>
                                    <option value="<?php echo intval($proveedor['id_proveedor']); ?>"><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Número de Factura</label>
                            <input type="text" class="form-control" name="numero_factura" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Compra</label>
                            <input type="datetime-local" class="form-control" name="fecha_compra" value="<?php echo date('Y-m-d\TH:i'); ?>">
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
                                <td><input type="number" step="0.01" class="form-control" name="subtotal[]" value="0.00" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="number" step="0.01" class="form-control" name="subtotal" value="0.00" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Impuesto</label>
                            <input type="number" step="0.01" class="form-control" name="impuesto" value="0.00" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" step="0.01" class="form-control" name="total" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="Registrada">Registrada</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Anulada">Anulada</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>