<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('caja');

try {
    $stmt = $conexion->query('SELECT id_caja, nombre, ubicacion, estado FROM cajas ORDER BY id_caja ASC');
    $cajas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $cajas = [];
    $error = 'No se pudieron cargar las cajas: ' . $e->getMessage();
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abrir Caja - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Abrir Caja</h2>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card"><div class="card-body">
        <form method="post" action="guardar_apertura.php">
          <div class="mb-3">
            <label class="form-label">Caja</label>
            <select name="fk_caja" class="form-select" required>
              <option value="">-- Seleccione una caja --</option>
              <?php foreach ($cajas as $c): ?>
                <option value="<?php echo intval($c['id_caja']); ?>"><?php echo htmlspecialchars($c['nombre'] . ' (' . $c['ubicacion'] . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Monto inicial</label>
            <input type="number" step="0.01" name="monto_inicial" class="form-control" value="0.00" required>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Abrir caja</button>
            <a href="caja.php" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
  </body>
  </html>
