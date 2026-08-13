<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAcceso('departamentos');

try {
    $stmt = $conexion->prepare("SELECT id_departamento, nombre, descripcion, estado FROM departamentos ORDER BY id_departamento DESC");
    $stmt->execute();
    $departamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $departamentos = [];
    $error = 'No se pudieron cargar los departamentos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Departamentos - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Departamentos</h2>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <ul class="list-group">
            <?php if (!empty($departamentos)) : ?>
              <?php foreach ($departamentos as $departamento) : ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>
                    <strong><?php echo htmlspecialchars($departamento['nombre']); ?></strong>
                    <?php if (!empty($departamento['descripcion'])) : ?>
                      <div class="text-muted small"><?php echo htmlspecialchars($departamento['descripcion']); ?></div>
                    <?php endif; ?>
                  </span>
                  <?php if (!empty($departamento['estado'])) : ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else : ?>
                    <span class="badge bg-secondary">Inactivo</span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            <?php else : ?>
              <li class="list-group-item text-muted">No hay departamentos registrados.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



