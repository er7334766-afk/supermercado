<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">

      <div class="d-flex justify-content-between align-items-center header-title">

          <h2>Usuarios</h2>

          <a href="nuevo_usuario.php" class="btn btn-primary">
              Nuevo usuario
          </a>

      </div>

      <div class="card shadow-sm">

          <div class="card-body">

              <table class="table table-striped table-hover align-middle">

                  <thead class="table-dark">

                      <tr>
                          <th>Nombre</th>
                          <th>Usuario</th>
                          <th>Correo</th>
                          <th>Teléfono</th>
                          <th>Rol</th>
                          <th>Estado</th>
                          <th>Acciones</th>
                      </tr>

                  </thead>

                  <tbody>

                      <tr>

                          <td>Juan Pérez</td>

                          <td>jperez</td>

                          <td>jperez@gmail.com</td>

                          <td>9999-9999</td>

                          <td>Administrador</td>

                          <td>
                              <span class="badge bg-success">
                                  Activo
                              </span>
                          </td>

                          <td>

                              <a
                                  href="editar_usuario.php?id=1"
                                  class="btn btn-sm btn-warning">

                                  Editar

                              </a>

                              <a
                                  href="eliminar_usuario.php?id=1"
                                  class="btn btn-sm btn-danger"
                                  onclick="return confirm('¿Está seguro de eliminar este usuario?');">

                                  Eliminar

                              </a>

                          </td>

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



