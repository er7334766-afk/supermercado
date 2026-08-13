<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('inventario');

try {
    $stmt = $conexion->prepare('SELECT m.*, p.nombre as producto, u.usuario as usuario FROM movimientos_inventario m LEFT JOIN productos p ON p.id_producto = m.fk_producto LEFT JOIN usuarios u ON u.id_usuario = m.fk_usuario ORDER BY m.fecha_movimiento DESC LIMIT 200');
    $stmt->execute();
    $movs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $movs = [];
    $error = 'No se pudieron cargar los movimientos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movimientos de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Movimientos de Inventario</h2>
      </div>
      <?php if (isset($error)) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <div class="card"><div class="card-body">
        <table class="table table-sm table-striped align-middle">
          <thead class="table-dark"><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Existencia Anterior</th><th>Existencia Nueva</th><th>Referencia</th><th>Usuario</th><th>Observación</th></tr></thead>
          <tbody>
            <?php if (!empty($movs)) : ?>
              <?php foreach ($movs as $m): ?>
                <tr>
                  <td><?php echo htmlspecialchars($m['fecha_movimiento']); ?></td>
                  <td><?php echo htmlspecialchars($m['producto']); ?></td>
                  <td><?php echo htmlspecialchars($m['tipo_movimiento']); ?></td>
                  <td><?php echo intval($m['cantidad']); ?></td>
                  <td><?php echo intval($m['existencia_anterior']); ?></td>
                  <td><?php echo intval($m['existencia_nueva']); ?></td>
                  <td><?php echo htmlspecialchars($m['referencia']); ?></td>
                  <td><?php echo htmlspecialchars($m['usuario']); ?></td>
                  <td><?php echo htmlspecialchars($m['observacion']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="9" class="text-muted">No hay movimientos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
