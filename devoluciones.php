<?php
session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conexion->prepare(
        "SELECT d.id_devolucion, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, d.motivo, d.fecha_devolucion, d.estado "
        . "FROM devoluciones d "
        . "LEFT JOIN ventas v ON v.id_venta = d.fk_venta "
        . "LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente "
        . "ORDER BY d.id_devolucion DESC"
    );
    $stmt->execute();
    $devoluciones = $stmt->fetchAll();
} catch (PDOException $e) {
    $devoluciones = [];
    $error = 'No se pudieron cargar las devoluciones: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devoluciones - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Devoluciones</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Nueva devolucion</a>
      </div>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="card">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($devoluciones)) : ?>
                <?php foreach ($devoluciones as $devolucion) : ?>
                  <tr>
                    <td><?php echo intval($devolucion['id_devolucion']); ?></td>
                    <td><?php echo htmlspecialchars(trim(($devolucion['cliente_nombre'] ?? '') . ' ' . ($devolucion['cliente_apellido'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars($devolucion['motivo']); ?></td>
                    <td><?php echo htmlspecialchars($devolucion['fecha_devolucion']); ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($devolucion['estado']); ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="5" class="text-center text-muted">No hay devoluciones registradas.</td></tr>
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
