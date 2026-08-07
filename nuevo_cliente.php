<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Cliente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
</head>

<body>

<?php include "menu/_layout_sidebar.php"; ?>

<main class="content">

    <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Nuevo Cliente</h2>

        <a href="clientes.php" class="btn btn-secondary">
            Volver
        </a>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="guardar_cliente.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input
                            type="text"
                            name="apellido"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Identidad</label>
                        <input type="text" name="identidad" class="form-control" placeholder="0801-2000-00000" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">RTN</label>
                        <input type="text" name="rtn" class="form-control" placeholder="0801-2000-00000" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>