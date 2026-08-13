<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('departamentos', 'editar');

$id_departamento = (int)($_GET['id'] ?? $_POST['id_departamento'] ?? 0);

if ($id_departamento <= 0) {
    header('Location: departamentos.php');
    exit;
}

/* Cargar departamento */
try {

    $stmt = $conexion->prepare("
        SELECT id_departamento, nombre, descripcion, estado
        FROM departamentos
        WHERE id_departamento = ?
    ");

    $stmt->execute([$id_departamento]);
    $departamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$departamento) {
        header('Location: departamentos.php');
        exit;
    }

} catch (PDOException $e) {
    $error = 'No se pudo cargar el departamento: ' . $e->getMessage();
}


/* Actualizar departamento */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = (int)($_POST['estado'] ?? 1);

    if ($nombre !== '') {

        try {

            $stmt = $conexion->prepare("
                UPDATE departamentos
                SET nombre = ?,
                    descripcion = ?,
                    estado = ?
                WHERE id_departamento = ?
            ");

            $stmt->execute([
                $nombre,
                $descripcion,
                $estado,
                $id_departamento
            ]);

            header('Location: departamentos.php');
            exit;

        } catch (PDOException $e) {

            $error = 'No se pudo actualizar el departamento: ' . $e->getMessage();
        }

    } else {

        $error = 'El nombre del departamento es obligatorio.';
    }
}
?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Departamento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">

</head>

<body>

<?php include "menu/_layout_sidebar.php"; ?>

<main class="content">

    <div class="d-flex justify-content-between align-items-center header-title">

        <h2>Editar Departamento</h2>

        <a href="departamentos.php" class="btn btn-secondary">
            Volver
        </a>

    </div>


    <?php if (!empty($error)) : ?>

        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST" action="editar_departamento.php">

                <input
                    type="hidden"
                    name="id_departamento"
                    value="<?php echo intval($departamento['id_departamento']); ?>">


                <div class="mb-3">

                    <label class="form-label">
                        Nombre del Departamento
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="nombre"
                        value="<?php echo htmlspecialchars($departamento['nombre']); ?>"
                        required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Descripción
                    </label>

                    <textarea
                        class="form-control"
                        name="descripcion"
                        rows="4"><?php echo htmlspecialchars($departamento['descripcion'] ?? ''); ?></textarea>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Estado
                    </label>

                    <select name="estado" class="form-select">

                        <option
                            value="1"
                            <?php echo $departamento['estado'] == 1 ? 'selected' : ''; ?>>

                            Activo

                        </option>

                        <option
                            value="0"
                            <?php echo $departamento['estado'] == 0 ? 'selected' : ''; ?>>

                            Inactivo

                        </option>

                    </select>

                </div>


                <div class="text-end">

                    <button type="submit" class="btn btn-success">
                        Guardar Cambios
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