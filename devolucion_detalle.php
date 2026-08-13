<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('devoluciones');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: devoluciones.php');
    exit;
}

try {
    $stmt = $conexion->prepare('SELECT d.*, v.numero_factura, v.fk_cliente, c.nombre as cliente_nombre, c.apellido as cliente_apellido FROM devoluciones d LEFT JOIN ventas v ON v.id_venta = d.fk_venta LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente WHERE d.id_devolucion = ? LIMIT 1');
    $stmt->execute([$id]);
    $devol = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $conexion->prepare('SELECT dd.fk_producto, dd.cantidad, dd.precio_unitario, dd.subtotal, p.nombre FROM detalle_devoluciones dd LEFT JOIN productos p ON p.id_producto = dd.fk_producto WHERE dd.fk_devolucion = ?');
    $stmt2->execute([$id]);
    $lineas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $devol = null;
    $lineas = [];
    $error = 'No se pudo cargar la devolución: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle Devolución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Detalle Devolución</h2>
        <a href="devoluciones.php" class="btn btn-secondary">Volver</a>
      </div>
      <?php if (isset($error)) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if (!$devol) : ?><div class="alert alert-warning">Devolución no encontrada.</div><?php else: ?>
      <div class="card"><div class="card-body">
        <p><strong>ID:</strong> <?php echo intval($devol['id_devolucion']); ?></p>
        <p><strong>Venta:</strong> <?php echo htmlspecialchars($devol['numero_factura'] ?? ''); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars(trim(($devol['cliente_nombre'] ?? '') . ' ' . ($devol['cliente_apellido'] ?? ''))); ?></p>
        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($devol['fecha_devolucion']); ?></p>
        <p><strong>Motivo:</strong> <?php echo htmlspecialchars($devol['motivo']); ?></p>
        <p><strong>Total devuelto:</strong> L. <?php echo number_format((float)($devol['total_devuelto'] ?? 0),2,'.',','); ?></p>

        <h5>Detalle</h5>
        <table class="table table-sm">
          <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
          <tbody>
            <?php if (!empty($lineas)) : ?>
              <?php foreach ($lineas as $l): ?>
                <tr>
                  <td><?php echo htmlspecialchars($l['nombre'] ?? ''); ?></td>
                  <td>L. <?php echo number_format((float)$l['precio_unitario'],2,'.',','); ?></td>
                  <td><?php echo intval($l['cantidad']); ?></td>
                  <td>L. <?php echo number_format((float)$l['subtotal'],2,'.',','); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-muted">No hay detalle (devolución total o sin líneas registradas).</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div></div>
      <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
