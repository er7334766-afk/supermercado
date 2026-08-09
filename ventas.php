<?php
require_once 'config/conexion.php';

$mensajeExito = '';
if (isset($_GET['success'])) {
    $mensajeExito = 'Registro guardado correctamente.';
}

try {
    $stmt = $conexion->prepare("SELECT v.id_venta, v.numero_factura, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, v.subtotal, v.descuento, v.impuesto, v.total, v.metodo_pago, v.fecha_venta, v.estado FROM ventas v LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente ORDER BY v.id_venta DESC");
    $stmt->execute();
    $ventas = $stmt->fetchAll();
} catch (PDOException $e) {
    $ventas = [];
    $error = 'No se pudieron cargar las ventas: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Ventas</h2>
        <a href="nueva_venta.php" class="btn btn-primary">Nueva venta</a>
      </div>

      <?php if (!empty($mensajeExito)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($mensajeExito); ?></div>
      <?php endif; ?>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Factura</th>
                <th>Cliente</th>
                <th>Subtotal</th>
                <th>Descuento</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th>Método de pago</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($ventas)) : ?>
                <?php foreach ($ventas as $venta) : ?>
                  <tr>
                    <td><?php echo intval($venta['id_venta']); ?></td>
                    <td><?php echo htmlspecialchars($venta['numero_factura']); ?></td>
                    <td><?php echo htmlspecialchars(trim(($venta['cliente_nombre'] ?? '') . ' ' . ($venta['cliente_apellido'] ?? ''))); ?></td>
                    <td>L. <?php echo number_format((float)$venta['subtotal'], 2, '.', ','); ?></td>
                    <td>L. <?php echo number_format((float)$venta['descuento'], 2, '.', ','); ?></td>
                    <td>L. <?php echo number_format((float)$venta['impuesto'], 2, '.', ','); ?></td>
                    <td><strong>L. <?php echo number_format((float)$venta['total'], 2, '.', ','); ?></strong></td>
                    <td><?php echo htmlspecialchars($venta['metodo_pago']); ?></td>
                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                    <td>
                      <?php if ($venta['estado'] === 'Completada') : ?>
                        <span class="badge bg-success">Completada</span>
                      <?php else : ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($venta['estado']); ?></span>
                      <?php endif; ?>
                    </td>
                    <td><a href="ver_venta.php?id=<?php echo intval($venta['id_venta']); ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="11" class="text-center text-muted">No hay ventas registradas.</td></tr>
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
