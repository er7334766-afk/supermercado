<!doctype html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nuevo Proveedor</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Nuevo Proveedor</h2>

            <a href="proveedores.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="guardar_proveedor.php" method="POST">

                    <div class="mb-3">

                        <label class="form-label">Nombre de la Empresa</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nombre_empresa"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Persona de Contacto</label>

                        <input
                            type="text"
                            class="form-control"
                            name="contacto">

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">RTN</label>

                            <input
                                type="text"
                                class="form-control"
                                name="rtn"
                                maxlength="20">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Teléfono</label>

                            <input
                                type="text"
                                class="form-control"
                                name="telefono">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Correo Electrónico</label>

                        <input
                            type="email"
                            class="form-control"
                            name="correo">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Dirección</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="direccion"></textarea>

                    </div>

                    <div class="text-end">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Guardar Proveedor

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>

    </body>
</html>