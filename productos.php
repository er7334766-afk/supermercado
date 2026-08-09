<?php
require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT p.id_producto, p.codigo_barras, p.nombre, p.descripcion, p.precio_compra, p.precio_venta, p.existencia, p.existencia_minima, p.unidad_medida, p.fecha_vencimiento, p.estado, d.nombre AS departamento FROM productos p LEFT JOIN departamentos d ON d.id_departamento = p.fk_departamento ORDER BY p.id_producto DESC");
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
    $error = 'No se pudieron cargar los productos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Productos - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Productos</h2>
        <a href="nuevo_producto.php" class="btn btn-primary">Nuevo Producto</a>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Compra</th>
                <th>Venta</th>
                <th>Existencia</th>
                <th>Mínimo</th>
                <th>Unidad</th>
                <th>Vence</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($productos)) : ?>
                <?php foreach ($productos as $producto) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($producto['codigo_barras']); ?></td>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($producto['departamento'] ?? 'Sin departamento'); ?></td>
                    <td>L. <?php echo number_format((float)$producto['precio_compra'], 2, '.', ','); ?></td>
                    <td>L. <?php echo number_format((float)$producto['precio_venta'], 2, '.', ','); ?></td>
                    <td><?php echo intval($producto['existencia']); ?></td>
                    <td><?php echo intval($producto['existencia_minima']); ?></td>
                    <td><?php echo htmlspecialchars($producto['unidad_medida']); ?></td>
                    <td><?php echo !empty($producto['fecha_vencimiento']) ? htmlspecialchars($producto['fecha_vencimiento']) : '—'; ?></td>
                    <td>
                      <?php if (!empty($producto['estado'])) : ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else : ?>
                        <span class="badge bg-secondary">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="editar_producto.php?id=<?php echo intval($producto['id_producto']); ?>" class="btn btn-warning btn-sm">Editar</a>
                      <a href="eliminar_producto.php?id=<?php echo intval($producto['id_producto']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desea eliminar este producto?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="11" class="text-center text-muted">No hay productos registrados.</td>
                </tr>
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
