<!doctype html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Editar Proveedor</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Proveedor</h2>

            <a href="proveedores.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="actualizar_proveedor.php" method="POST">

                    <input type="hidden" name="id_proveedor" value="1">

                    <div class="mb-3">

                        <label class="form-label">Nombre de la Empresa</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nombre_empresa"
                            value="Proveedor X"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Persona de Contacto</label>

                        <input
                            type="text"
                            class="form-control"
                            name="contacto"
                            value="Juan Pérez">

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">RTN</label>

                            <input
                                type="text"
                                class="form-control"
                                name="rtn"
                                value="08011999123456">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Teléfono</label>

                            <input
                                type="text"
                                class="form-control"
                                name="telefono"
                                value="9999-9999">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Correo Electrónico</label>

                        <input
                            type="email"
                            class="form-control"
                            name="correo"
                            value="contacto@provx.com">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Dirección</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="direccion">Tegucigalpa, Honduras</textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Estado</label>

                        <select
                            class="form-select"
                            name="estado">

                            <option value="1" selected>
                                Activo
                            </option>

                            <option value="0">
                                Inactivo
                            </option>

                        </select>

                    </div>

                    <div class="text-end">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Actualizar Proveedor

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