<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

$mensajeExito = '';
if (isset($_GET['success'])) {
    $mensajeExito = 'Registro guardado correctamente.';
}

try {
    $stmt = $conexion->prepare("SELECT c.id_compra, c.numero_factura, p.nombre_empresa AS proveedor_nombre, c.subtotal, c.impuesto, c.total, c.fecha_compra, c.estado FROM compras c LEFT JOIN proveedores p ON p.id_proveedor = c.fk_proveedor ORDER BY c.id_compra DESC");
    $stmt->execute();
    $compras = $stmt->fetchAll();
} catch (PDOException $e) {
    $compras = [];
    $error = 'No se pudieron cargar las compras: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compras - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Compras</h2>
        <a href="nueva_compra.php" class="btn btn-primary">Nueva compra</a>
      </div>
      <?php if (!empty($mensajeExito)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($mensajeExito); ?></div>
      <?php endif; ?>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="card">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Factura</th>
                <th>Proveedor</th>
                <th>Subtotal</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($compras)) : ?>
                <?php foreach ($compras as $compra) : ?>
                  <tr>
                    <td><?php echo intval($compra['id_compra']); ?></td>
                    <td><?php echo htmlspecialchars($compra['numero_factura']); ?></td>
                    <td><?php echo htmlspecialchars($compra['proveedor_nombre'] ?? 'Sin proveedor'); ?></td>
                    <td>L. <?php echo number_format((float)$compra['subtotal'], 2, '.', ','); ?></td>
                    <td>L. <?php echo number_format((float)$compra['impuesto'], 2, '.', ','); ?></td>
                    <td><strong>L. <?php echo number_format((float)$compra['total'], 2, '.', ','); ?></strong></td>
                    <td><?php echo htmlspecialchars($compra['fecha_compra']); ?></td>
                    <td><span class="badge bg-success"><?php echo htmlspecialchars($compra['estado']); ?></span></td>
                    <td><a href="editar_compra.php?id=<?php echo intval($compra['id_compra']); ?>" class="btn btn-sm btn-warning">Editar</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="9" class="text-center text-muted">No hay compras registradas.</td></tr>
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
