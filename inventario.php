<?php
session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conexion->prepare('SELECT p.codigo_barras, p.nombre, p.existencia, p.unidad_medida, d.nombre AS departamento FROM productos p LEFT JOIN departamentos d ON d.id_departamento = p.fk_departamento WHERE p.estado = 1 ORDER BY p.nombre ASC');
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Inventario</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Ajuste de stock</a>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered">
            <thead><tr><th>Codigo</th><th>Producto</th><th>Stock</th><th>Unidad</th><th>Departamento</th></tr></thead>
            <tbody>
              <?php if (!empty($productos)) : ?>
                <?php foreach ($productos as $producto) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($producto['codigo_barras']); ?></td>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo intval($producto['existencia']); ?></td>
                    <td><?php echo htmlspecialchars($producto['unidad_medida']); ?></td>
                    <td><?php echo htmlspecialchars($producto['departamento'] ?? '-'); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="5" class="text-center text-muted">No hay productos en inventario.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



