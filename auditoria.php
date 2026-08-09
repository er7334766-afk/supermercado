<?php
session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conexion->prepare(
        "SELECT a.id_auditoria, u.usuario AS usuario, a.accion, a.descripcion AS detalle, a.fecha "
        . "FROM auditoria a LEFT JOIN usuarios u ON u.id_usuario = a.fk_usuario ORDER BY a.id_auditoria DESC"
    );
    $stmt->execute();
    $auditorias = $stmt->fetchAll();
} catch (PDOException $e) {
    $auditorias = [];
    $error = 'No se pudo cargar la auditoría: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoria - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title"><h2>Auditoria</h2></div>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="card">
        <div class="card-body">
          <ul class="list-group">
            <?php if (!empty($auditorias)) : ?>
              <?php foreach ($auditorias as $registro) : ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                      <strong><?php echo htmlspecialchars($registro['fecha_registro']); ?></strong>
                      <div><?php echo htmlspecialchars($registro['usuario'] ?? 'Sistema'); ?> - <?php echo htmlspecialchars($registro['accion']); ?></div>
                      <?php if (!empty($registro['detalle'])) : ?>
                        <small class="text-muted"><?php echo htmlspecialchars($registro['detalle']); ?></small>
                      <?php endif; ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php else : ?>
              <li class="list-group-item text-muted">No hay registros de auditoría.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>
