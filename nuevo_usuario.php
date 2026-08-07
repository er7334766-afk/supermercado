<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuevo Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
</head>

<body>

<?php include "menu/_layout_sidebar.php"; ?>

<main class="content">

    <div class="d-flex justify-content-between align-items-center header-title">

        <h2>Nuevo Usuario</h2>

        <a href="usuarios.php" class="btn btn-secondary">
            Volver
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="guardar_usuario.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Nombre</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nombre"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Apellido</label>

                        <input
                            type="text"
                            class="form-control"
                            name="apellido"
                            required>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Correo Electrónico</label>

                        <input
                            type="email"
                            class="form-control"
                            name="correo"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Teléfono</label>

                        <input
                            type="text"
                            class="form-control"
                            name="telefono">

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Usuario</label>

                        <input
                            type="text"
                            class="form-control"
                            name="usuario"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Contraseña</label>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            required>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Rol</label>

                        <select
                            class="form-select"
                            name="fk_rol"
                            required>

                            <option value="">Seleccione...</option>

                            <option value="1">Administrador</option>

                            <option value="2">Cajero</option>

                            <option value="3">Supervisor</option>

                            <option value="4">Bodega</option>

                        </select>

                    </div>


                </div>

                <div class="text-end">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Guardar Usuario

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