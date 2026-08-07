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

          <a href="nueva_compra.php" class="btn btn-primary">
              Nueva compra
          </a>

      </div>

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

                      <tr>

                          <td>5001</td>

                          <td>COMP-000001</td>

                          <td>Proveedor X</td>

                          <td>L. 400.00</td>

                          <td>L. 60.00</td>

                          <td>
                              <strong>L. 460.00</strong>
                          </td>

                          <td>2026-07-30</td>

                          <td>
                              <span class="badge bg-success">
                                  Registrada
                              </span>
                          </td>

                          <td>

                              <a
                                  href="editar_compra.php?id=5001"
                                  class="btn btn-sm btn-warning">

                                  Editar

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



