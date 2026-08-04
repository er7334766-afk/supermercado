<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <!-- Sidebar copied -->
    <?php include "_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Clientes</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Nuevo cliente</a>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-hover">
            <thead><tr><th>Nombre</th><th>Email</th><th>Telefono</th><th>Acciones</th></tr></thead>
            <tbody>
              <tr><td>ACME S.A.</td><td>contacto@acme.com</td><td>+341234567</td><td><a href="javascript:void(0)" data-action="coming-soon" class="btn btn-sm btn-outline-primary">Editar</a></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>



