<?php
session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $ventasResumen = $conexion->query("SELECT COUNT(*) AS total_ventas, COALESCE(SUM(total), 0) AS total_monto FROM ventas WHERE estado = 'Completada'")->fetch();
    $inventarioResumen = $conexion->query("SELECT COUNT(*) AS productos_bajos FROM productos WHERE estado = 1 AND existencia <= 5")->fetch();
    $ventasRecientes = $conexion->query("SELECT numero_factura, total, fecha_venta FROM ventas ORDER BY id_venta DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $ventasResumen = ['total_ventas' => 0, 'total_monto' => 0];
    $inventarioResumen = ['productos_bajos' => 0];
    $ventasRecientes = [];
    $error = 'No se pudieron cargar los reportes: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Reportes</h2>
      </div>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card p-3 h-100">
            <h5>Ventas</h5>
            <p class="mb-1 text-muted">Ventas completadas</p>
            <h3><?php echo intval($ventasResumen['total_ventas']); ?></h3>
            <p class="mb-0 text-success">Monto total: L. <?php echo number_format((float)$ventasResumen['total_monto'], 2, '.', ','); ?></p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-3 h-100">
            <h5>Inventario</h5>
            <p class="mb-1 text-muted">Productos con stock bajo</p>
            <h3><?php echo intval($inventarioResumen['productos_bajos']); ?></h3>
            <p class="mb-0 text-warning">Revisión recomendada</p>
          </div>
        </div>
      </div>
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="mb-3">Últimas ventas</h5>
          <table class="table table-striped align-middle">
            <thead class="table-dark">
              <tr><th>Factura</th><th>Total</th><th>Fecha</th></tr>
            </thead>
            <tbody>
              <?php if (!empty($ventasRecientes)) : ?>
                <?php foreach ($ventasRecientes as $venta) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($venta['numero_factura']); ?></td>
                    <td>L. <?php echo number_format((float)$venta['total'], 2, '.', ','); ?></td>
                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="3" class="text-center text-muted">No hay ventas registradas.</td></tr>
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



