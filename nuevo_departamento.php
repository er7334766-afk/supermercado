<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('departamentos', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre !== '') {

        try {

            // Verificar que no exista otro departamento con el mismo nombre
            $stmt = $conexion->prepare("
                SELECT id_departamento
                FROM departamentos
                WHERE nombre = ?
                LIMIT 1
            ");

            $stmt->execute([$nombre]);

            if ($stmt->fetch()) {

                $error = 'Ya existe un departamento con ese nombre.';

            } else {

                $stmt = $conexion->prepare("
                    INSERT INTO departamentos (
                        nombre,
                        descripcion,
                        estado
                    )
                    VALUES (?, ?, 1)
                ");

                $stmt->execute([
                    $nombre,
                    $descripcion
                ]);

                header('Location: departamentos.php?success=1');
                exit;
            }

        } catch (PDOException $e) {

            $error = 'No se pudo guardar el departamento: ' . $e->getMessage();
        }

    } else {

        $error = 'Ingresa el nombre del departamento.';
    }
}
?>

<!doctype html>
<html lang="es">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nuevo Departamento</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Nuevo Departamento</h2>

            <a href="departamentos.php"
            class="btn btn-secondary">

                Volver

            </a>

        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <?php if (!empty($error)) : ?>

                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>

                <form action="nuevo_departamento.php" method="POST">
                    <div class="mb-3">

                        <label class="form-label">
                            Nombre del Departamento
                        </label>

                        <input type="text" class="form-control" name="nombre" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label"> Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="4"></textarea>

                    </div>

                    <div class="text-end">

                        <button type="submit" class="btn btn-success">Guardar Departamento </button>

                    </div>

                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>

    </body>
    
</html>