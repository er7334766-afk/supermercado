<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('productos', 'editar');

$producto = [
    'id_producto' => 0,
    'fk_departamento' => 0,
    'codigo_barras' => '',
    'nombre' => '',
    'descripcion' => '',
    'precio_compra' => 0,
    'precio_venta' => 0,
    'existencia' => 0,
    'existencia_minima' => 5,
    'unidad_medida' => 'Unidad',
    'fecha_vencimiento' => '',
    'estado' => 1
];

$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $conexion->prepare("SELECT id_departamento, nombre FROM departamentos WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $departamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $departamentos = [];
    $error = 'No se pudieron cargar los departamentos: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int)($_POST['id_producto'] ?? 0);
    $producto['fk_departamento'] = (int)($_POST['fk_departamento'] ?? 0);
    $producto['codigo_barras'] = trim($_POST['codigo_barras'] ?? '');
    $producto['nombre'] = trim($_POST['nombre'] ?? '');
    $producto['descripcion'] = trim($_POST['descripcion'] ?? '');
    $producto['precio_compra'] = (float)($_POST['precio_compra'] ?? 0);
    $producto['precio_venta'] = (float)($_POST['precio_venta'] ?? 0);
    $producto['existencia'] = (int)($_POST['existencia'] ?? 0);
    $producto['existencia_minima'] = (int)($_POST['existencia_minima'] ?? 5);
    $producto['unidad_medida'] = trim($_POST['unidad_medida'] ?? 'Unidad');
    $producto['fecha_vencimiento'] = trim($_POST['fecha_vencimiento'] ?? '');
    $producto['estado'] = (int)($_POST['estado'] ?? 1);

    if ($id_producto > 0 && $producto['fk_departamento'] > 0 && $producto['codigo_barras'] !== '' && $producto['nombre'] !== '') {
        try {
            $stmt = $conexion->prepare("UPDATE productos SET fk_departamento = ?, codigo_barras = ?, nombre = ?, descripcion = ?, precio_compra = ?, precio_venta = ?, existencia = ?, existencia_minima = ?, unidad_medida = ?, fecha_vencimiento = ?, estado = ? WHERE id_producto = ?");
            $stmt->execute([$producto['fk_departamento'], $producto['codigo_barras'], $producto['nombre'], $producto['descripcion'], $producto['precio_compra'], $producto['precio_venta'], $producto['existencia'], $producto['existencia_minima'], $producto['unidad_medida'], $producto['fecha_vencimiento'], $producto['estado'], $id_producto]);
            header('Location: productos.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo actualizar el producto: ' . $e->getMessage();
        }
    } else {
        $error = 'Completa los campos obligatorios.';
    }
}

if ($id_producto > 0) {
    try {
        $stmt = $conexion->prepare("SELECT id_producto, fk_departamento, codigo_barras, nombre, descripcion, precio_compra, precio_venta, existencia, existencia_minima, unidad_medida, fecha_vencimiento, estado FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC) ?: $producto;
    } catch (PDOException $e) {
        $error = 'No se pudo cargar el producto: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Producto</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Producto</h2>

            <a href="productos.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="editar_producto.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id_producto" value="<?php echo intval($producto['id_producto']); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="fk_departamento">
                                <?php foreach ($departamentos as $departamento) : ?>
                                    <option value="<?php echo intval($departamento['id_departamento']); ?>" <?php echo ((int)$producto['fk_departamento'] === (int)$departamento['id_departamento']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($departamento['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" class="form-control" name="codigo_barras" value="<?php echo htmlspecialchars($producto['codigo_barras']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" rows="3" name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio Compra</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra" value="<?php echo htmlspecialchars((string)$producto['precio_compra']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio Venta</label>
                            <input type="number" step="0.01" class="form-control" name="precio_venta" value="<?php echo htmlspecialchars((string)$producto['precio_venta']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Existencia</label>
                            <input type="number" class="form-control" name="existencia" value="<?php echo intval($producto['existencia']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Existencia Mínima</label>
                            <input type="number" class="form-control" name="existencia_minima" value="<?php echo intval($producto['existencia_minima']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <select class="form-select" name="unidad_medida">
                                <?php $unidad = $producto['unidad_medida']; ?>
                                <option <?php echo ($unidad === 'Unidad') ? 'selected' : ''; ?>>Unidad</option>
                                <option <?php echo ($unidad === 'Libra') ? 'selected' : ''; ?>>Libra</option>
                                <option <?php echo ($unidad === 'Kilogramo') ? 'selected' : ''; ?>>Kilogramo</option>
                                <option <?php echo ($unidad === 'Litro') ? 'selected' : ''; ?>>Litro</option>
                                <option <?php echo ($unidad === 'Caja') ? 'selected' : ''; ?>>Caja</option>
                                <option <?php echo ($unidad === 'Paquete') ? 'selected' : ''; ?>>Paquete</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="fecha_vencimiento" value="<?php echo htmlspecialchars($producto['fecha_vencimiento']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="1" <?php echo ((int)$producto['estado'] === 1) ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo ((int)$producto['estado'] === 0) ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Actualizar Producto</button>
                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>