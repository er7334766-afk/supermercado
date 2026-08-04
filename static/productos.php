<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Productos - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include "_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Productos</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Nuevo producto</a>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-striped">
            <thead><tr><th>Codigo</th><th>Nombre</th><th>Stock</th><th>Precio</th><th>Acciones</th></tr></thead>
            <tbody>
              <tr><td>P-001</td><td>Producto 1</td><td>12</td><td>$9.99</td><td><a class="btn btn-sm btn-outline-primary" href="javascript:void(0)" data-action="coming-soon">Editar</a></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>



