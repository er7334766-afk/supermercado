<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caja - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">

      <div class="header-title d-flex justify-content-between align-items-center">
    
          <h2>Caja</h2>

          <a class="btn btn-primary"
            href="javascript:void(0)"
            data-action="coming-soon">

              Cerrar turno

          </a>

      </div>
      <!-- TENGO DUDA PORQUE EN LA BD NO HAY PARA ALMACENAR EL SALGO ACTUAL -->

      <div class="card shadow-sm">

          <div class="card-body">

              <h5 class="card-title mb-4">
                  Información de Caja
              </h5>

              <div class="row">

                  <div class="col-md-6 mb-3">

                      <p class="mb-1 text-muted">
                          Nombre de Caja
                      </p>

                      <strong>
                          Caja Principal
                      </strong>

                  </div>

                  <div class="col-md-6 mb-3">

                      <p class="mb-1 text-muted">
                          Ubicación
                      </p>

                      <strong>
                          Pasillo A
                      </strong>

                  </div>

              </div>

              <div class="row">

                  <div class="col-md-6 mb-3">

                      <p class="mb-1 text-muted">
                          Estado
                      </p>

                      <span class="badge bg-success">
                          Abierta
                      </span>

                  </div>

                  <div class="col-md-6 mb-3">

                      <p class="mb-1 text-muted">
                          Saldo Actual
                      </p>

                      <h4 class="text-success mb-0">
                          L. 1,234.00
                      </h4>

                  </div>

              </div>

          </div>

      </div>

  </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



