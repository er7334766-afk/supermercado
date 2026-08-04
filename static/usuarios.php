<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include "_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Usuarios</h2>
        <a href="javascript:void(0)" data-action="coming-soon" class="btn btn-primary">Nuevo usuario</a>
      </div>
      <div class="card"><div class="card-body">
        <table class="table"><thead><tr><th>Nombre</th><th>Rol</th><th>Estado</th></tr></thead>
        <tbody><tr><td>Admin</td><td>Administrador</td><td>Activo</td></tr></tbody></table>
      </div></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>



