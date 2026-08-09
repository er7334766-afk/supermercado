<?php
session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conexion->query("SELECT id_caja, nombre, ubicacion, estado, saldo_inicial, saldo_actual FROM cajas ORDER BY id_caja DESC LIMIT 1");
    $caja = $stmt->fetch();
    if (!$caja) {
        $caja = [
            'id_caja' => 0,
            'nombre' => 'Sin caja registrada',
            'ubicacion' => '-',
            'estado' => 'Cerrada',
            'saldo_inicial' => 0,
            'saldo_actual' => 0,
        ];
    }
} catch (PDOException $e) {
    $caja = [
        'id_caja' => 0,
        'nombre' => 'Sin caja registrada',
        'ubicacion' => '-',
        'estado' => 'Cerrada',
        'saldo_inicial' => 0,
        'saldo_actual' => 0,
    ];
    $error = 'No se pudo cargar la caja: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caja - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Caja</h2>
        <a class="btn btn-primary" href="javascript:void(0)" data-action="coming-soon">Cerrar turno</a>
      </div>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4">Información de Caja</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 text-muted">Nombre de Caja</p>
              <strong><?php echo htmlspecialchars($caja['nombre']); ?></strong>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 text-muted">Ubicación</p>
              <strong><?php echo htmlspecialchars($caja['ubicacion']); ?></strong>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <p class="mb-1 text-muted">Estado</p>
              <span class="badge bg-success"><?php echo htmlspecialchars($caja['estado']); ?></span>
            </div>
            <div class="col-md-6 mb-3">
              <p class="mb-1 text-muted">Saldo Actual</p>
              <h4 class="text-success mb-0">L. <?php echo number_format((float)$caja['saldo_actual'], 2, '.', ','); ?></h4>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>
