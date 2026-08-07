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
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Clientes</h2>
        <a href="nuevo_cliente.php" class="btn btn-primary"> Nuevo cliente </a>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>RTN</th>
                <th>Telefono</th>
                <th>Direccion</th>
                <th>Acciones</th>

              </tr>
          </thead>

            <tbody>
              <tr>
                <td>ACME S.A.</td>
                <td>Acme</td>
                <td>contacto@acme.com</td>
                <td>+341234567</td>
                <td>123456789012345</td>
                <td>
                  <a href="editar_cliente.php?id=1" class="btn btn-sm btn-warning">Editar</a> 
                  <a href="eliminar_cliente.php?id=1" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este cliente?');">Eliminar</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



