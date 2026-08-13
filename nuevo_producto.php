<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('productos', 'crear');

try {
    $stmt = $conexion->prepare("SELECT id_departamento, nombre FROM departamentos WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $departamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $departamentos = [];
    $error = 'No se pudieron cargar los departamentos: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fk_departamento = (int)($_POST['fk_departamento'] ?? 0);
    $codigo_barras = trim($_POST['codigo_barras'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $existencia = (int)($_POST['existencia'] ?? 0);
    $existencia_minima = (int)($_POST['existencia_minima'] ?? 5);
    $unidad_medida = trim($_POST['unidad_medida'] ?? 'Unidad');
    $fecha_vencimiento = trim($_POST['fecha_vencimiento'] ?? '');

    if ($fk_departamento > 0 && $codigo_barras !== '' && $nombre !== '') {
        try {
            $stmt = $conexion->prepare("INSERT INTO productos (fk_departamento, codigo_barras, nombre, descripcion, precio_compra, precio_venta, existencia, existencia_minima, unidad_medida, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$fk_departamento, $codigo_barras, $nombre, $descripcion, $precio_compra, $precio_venta, $existencia, $existencia_minima, $unidad_medida, $fecha_vencimiento]);
            header('Location: productos.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo guardar el producto: ' . $e->getMessage();
        }
    } else {
        $error = 'Completa los campos obligatorios.';
    }
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nuevo Producto</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Nuevo Producto</h2>

            <a href="productos.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="nuevo_producto.php" method="POST" enctype="multipart/form-data">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="fk_departamento" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($departamentos as $departamento) : ?>
                                    <option value="<?php echo intval($departamento['id_departamento']); ?>"><?php echo htmlspecialchars($departamento['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" class="form-control" name="codigo_barras" required>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" rows="3" name="descripcion"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio Compra</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio Venta</label>
                            <input type="number" step="0.01" class="form-control" name="precio_venta" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Existencia</label>
                            <input type="number" class="form-control" name="existencia" value="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Existencia Mínima</label>
                            <input type="number" class="form-control" name="existencia_minima" value="5" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <select class="form-select" name="unidad_medida">
                                <option>Unidad</option>
                                <option>Libra</option>
                                <option>Kilogramo</option>
                                <option>Litro</option>
                                <option>Caja</option>
                                <option>Paquete</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="fecha_vencimiento">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar Producto</button>
                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>