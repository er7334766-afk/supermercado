<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compras - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include "_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Compras</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Nueva compra</a>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table">
            <thead><tr><th>ID</th><th>Proveedor</th><th>Total</th><th>Fecha</th></tr></thead>
            <tbody>
              <tr><td>5001</td><td>Proveedor X</td><td>$450.00</td><td>2026-07-30</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>



