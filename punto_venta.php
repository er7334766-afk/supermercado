<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('punto_venta');

try {
    $stmt = $conexion->prepare('SELECT id_producto, nombre, precio_venta, existencia FROM productos WHERE estado = 1 ORDER BY nombre ASC');
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Punto de Venta - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center"><h2>Punto de Venta</h2><a href="ventas.php" class="btn btn-primary">Ver ventas</a></div>
      <div class="card p-3">
        <div class="row">
          <div class="col-md-8">
            <h5>Productos disponibles</h5>
            <div class="row g-3">
              <?php if (!empty($productos)) : ?>
                <?php foreach ($productos as $producto) : ?>
                  <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                      <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                      <div class="text-muted small">Stock: <?php echo intval($producto['existencia']); ?></div>
                      <div class="text-success">L. <?php echo number_format((float)$producto['precio_venta'], 2, '.', ','); ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="col-12 text-muted">No hay productos disponibles.</div>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4">
            <h5>Resumen</h5>
            <div class="border rounded p-3">
              <p class="mb-2">Cliente: Consumidor Final</p>
              <p class="mb-2">Método de pago: Efectivo</p>
              <p class="mb-0">Total estimado: L. 0.00</p>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



