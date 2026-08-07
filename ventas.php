<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">

      <div class="d-flex justify-content-between align-items-center header-title">

          <h2>Ventas</h2>

          <a href="nueva_venta.php" class="btn btn-primary">
              Nueva venta
          </a>

      </div>

      <div class="card shadow-sm">

          <div class="card-body">

              <table class="table table-striped table-hover align-middle">

                  <thead class="table-dark">

                      <tr>
                          <th>ID</th>
                          <th>Factura</th>
                          <th>Cliente</th>
                          <th>Subtotal</th>
                          <th>Descuento</th>
                          <th>Impuesto</th>
                          <th>Total</th>
                          <th>Método de pago</th>
                          <th>Fecha</th>
                          <th>Estado</th>
                          <th>Acciones</th>
                      </tr>

                  </thead>

                  <tbody>

                      <tr>

                          <td>1001</td>

                          <td>FAC-000001</td>

                          <td>Consumidor Final</td>

                          <td>L. 100.00</td>

                          <td>L. 0.00</td>

                          <td>L. 15.00</td>

                          <td>
                              <strong>L. 115.00</strong>
                          </td>

                          <td>Efectivo</td>

                          <td>2026-08-06</td>

                          <td>
                              <span class="badge bg-success">
                                  Completada
                              </span>
                          </td>

                          <td>

                              <a
                                  href="ver_venta.php?id=1001"
                                  class="btn btn-sm btn-outline-primary">

                                  Ver

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



