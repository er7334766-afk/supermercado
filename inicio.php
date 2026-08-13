<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/mensajes.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('inicio');

$mensajeAccesoDenegado = mostrarMensajeAccesoDenegado();

if (isset($_GET['error'])) {
    $errorLogin = 'Usuario o contraseña incorrecta';
} else {
    $errorLogin = '';
}

try {
    $stmt = $conexion->query("SELECT COUNT(*) AS total_clientes FROM clientes WHERE estado = 1");
    $totalClientes = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalClientes = 0;
}

try {
    $stmt = $conexion->query("SELECT COUNT(*) AS total_productos FROM productos WHERE estado = 1");
    $totalProductos = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalProductos = 0;
}

try {
    $stmt = $conexion->query("SELECT COALESCE(SUM(total), 0) AS ventas_hoy FROM ventas WHERE DATE(fecha_venta) = CURDATE()");
    $ventasHoy = (float) $stmt->fetchColumn();
} catch (PDOException $e) {
    $ventasHoy = 0;
}

try {
    $stmt = $conexion->prepare(
        "SELECT v.id_venta, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, v.total, v.estado "
        . "FROM ventas v LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente ORDER BY v.id_venta DESC LIMIT 5"
    );
    $stmt->execute();
    $ventasRecientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $ventasRecientes = [];
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <?php if (!empty($errorLogin)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorLogin); ?></div>
      <?php endif; ?>
      <?php if (!empty($mensajeAccesoDenegado)) : ?>
        <div class="alert alert-warning" role="alert">
          <strong>Acceso denegado:</strong> <?php echo htmlspecialchars($mensajeAccesoDenegado); ?>
        </div>
      <?php endif; ?>
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Inicio</h2>
        <div>
          <button class="btn btn-outline-secondary me-2">Ayuda</button>
          <button class="btn btn-primary">Nuevo</button>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Ventas hoy</h5>
            <p class="h2">L. <?php echo number_format($ventasHoy, 2, '.', ','); ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Clientes</h5>
            <p class="h2"><?php echo intval($totalClientes); ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Productos</h5>
            <p class="h2"><?php echo intval($totalProductos); ?></p>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <h5>Ultimas ventas</h5>
          <table class="table table-sm mt-3">
            <thead>
              <tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th></tr>
            </thead>
            <tbody>
              <?php if (!empty($ventasRecientes)) : ?>
                <?php foreach ($ventasRecientes as $venta) : ?>
                  <tr>
                    <td><?php echo intval($venta['id_venta']); ?></td>
                    <td><?php echo htmlspecialchars(trim(($venta['cliente_nombre'] ?? '') . ' ' . ($venta['cliente_apellido'] ?? ''))); ?></td>
                    <td>L. <?php echo number_format((float)$venta['total'], 2, '.', ','); ?></td>
                    <td><?php echo htmlspecialchars($venta['estado']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="4" class="text-center text-muted">No hay ventas recientes.</td></tr>
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
