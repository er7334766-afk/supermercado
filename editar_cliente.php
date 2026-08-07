<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Cliente</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Cliente</h2>

            <a href="clientes.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="actualizar_cliente.php" method="POST">

                    <input type="hidden" name="id_cliente" value="1">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Nombre</label>

                            <input
                                type="text"
                                class="form-control"
                                name="nombre"
                                value="Juan">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Apellido</label>

                            <input
                                type="text"
                                class="form-control"
                                name="apellido"
                                value="Pérez">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Identidad</label>

                            <input
                                type="text"
                                class="form-control"
                                name="identidad"
                                value="0801-2000-12345">

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

                        <label class="form-label">Correo</label>

                        <input
                            type="email"
                            class="form-control"
                            name="correo"
                            value="juan@gmail.com">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Dirección</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="direccion">San Pedro Sula</textarea>

                    </div>

                    <div class="text-end">

                        <button class="btn btn-success">

                            Actualizar Cliente

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