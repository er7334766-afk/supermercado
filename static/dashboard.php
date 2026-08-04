<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include "_layout_sidebar.php"; ?>

    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Dashboard</h2>
        <div>
          <button class="btn btn-outline-secondary me-2">Ayuda</button>
          <button class="btn btn-primary">Nuevo</button>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Ventas hoy</h5>
            <p class="h2">$4,230</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Clientes</h5>
            <p class="h2">1,234</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Productos</h5>
            <p class="h2">512</p>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <h5>Ultimas ventas</h5>
          <table class="table table-sm mt-3">
            <thead>
              <tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th></tr>
            </thead>
            <tbody>
              <tr><td>1</td><td>ACME</td><td>$120.00</td><td>Completado</td></tr>
              <tr><td>2</td><td>Beta</td><td>$89.50</td><td>Procesando</td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>



