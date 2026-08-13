<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('facturacion');

try {
    $stmt = $conexion->prepare('SELECT v.id_venta, v.numero_factura, CONCAT(COALESCE(c.nombre, ""), " ", COALESCE(c.apellido, "")) AS cliente, v.fecha_venta, v.total, v.estado FROM ventas v LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente ORDER BY v.id_venta DESC');
    $stmt->execute();
    $facturas = $stmt->fetchAll();
} catch (PDOException $e) {
    $facturas = [];
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facturacion - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center"><h2>Facturacion</h2><a class="btn btn-primary" href="ventas.php">Nueva factura</a></div>
      <div class="card"><div class="card-body">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr><th>#</th><th>Factura</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($facturas)) : ?>
              <?php foreach ($facturas as $factura) : ?>
                <tr>
                  <td><?php echo intval($factura['id_venta']); ?></td>
                  <td><?php echo htmlspecialchars($factura['numero_factura']); ?></td>
                  <td><?php echo htmlspecialchars(trim($factura['cliente'])); ?></td>
                  <td><?php echo htmlspecialchars($factura['fecha_venta']); ?></td>
                  <td>L. <?php echo number_format((float)$factura['total'], 2, '.', ','); ?></td>
                  <td><?php echo htmlspecialchars($factura['estado']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="6" class="text-center text-muted">No hay facturas registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



