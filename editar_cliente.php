<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAccesoAccion('clientes', 'editar');

$cliente = [
    'id_cliente' => 0,
    'nombre' => '',
    'apellido' => '',
    'identidad' => '',
    'rtn' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => ''
];

$id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
    $cliente['id_cliente'] = $id_cliente;
    $cliente['nombre'] = trim($_POST['nombre'] ?? '');
    $cliente['apellido'] = trim($_POST['apellido'] ?? '');
    $cliente['identidad'] = trim($_POST['identidad'] ?? '');
    $cliente['rtn'] = trim($_POST['rtn'] ?? '');
    $cliente['telefono'] = trim($_POST['telefono'] ?? '');
    $cliente['correo'] = trim($_POST['correo'] ?? '');
    $cliente['direccion'] = trim($_POST['direccion'] ?? '');

    if ($id_cliente > 0 && $cliente['nombre'] !== '' && $cliente['apellido'] !== '' && $cliente['correo'] !== '') {
        try {
            $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, apellido = ?, identidad = ?, rtn = ?, telefono = ?, correo = ?, direccion = ? WHERE id_cliente = ?");
            $stmt->execute([$cliente['nombre'], $cliente['apellido'], $cliente['identidad'], $cliente['rtn'], $cliente['telefono'], $cliente['correo'], $cliente['direccion'], $id_cliente]);
            header('Location: clientes.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo actualizar el cliente: ' . $e->getMessage();
        }
    } else {
        $error = 'Completa los campos obligatorios.';
    }
}

if ($id_cliente > 0) {
    try {
        $stmt = $conexion->prepare("SELECT id_cliente, nombre, apellido, identidad, rtn, telefono, correo, direccion FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC) ?: $cliente;
    } catch (PDOException $e) {
        $error = 'No se pudo cargar el cliente: ' . $e->getMessage();
    }
}
?>
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

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="editar_cliente.php" method="POST">

                    <input type="hidden" name="id_cliente" value="<?php echo intval($cliente['id_cliente']); ?>">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Nombre</label>

                            <input
                                type="text"
                                class="form-control"
                                name="nombre"
                                value="<?php echo htmlspecialchars($cliente['nombre']); ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Apellido</label>

                            <input
                                type="text"
                                class="form-control"
                                name="apellido"
                                value="<?php echo htmlspecialchars($cliente['apellido']); ?>">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Identidad</label>

                            <input
                                type="text"
                                class="form-control"
                                name="identidad"
                                value="<?php echo htmlspecialchars($cliente['identidad']); ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">RTN</label>

                            <input
                                type="text"
                                class="form-control"
                                name="rtn"
                                value="<?php echo htmlspecialchars($cliente['rtn']); ?>">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Teléfono</label>

                            <input
                                type="text"
                                class="form-control"
                                name="telefono"
                                value="<?php echo htmlspecialchars($cliente['telefono']); ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Correo</label>

                            <input
                                type="email"
                                class="form-control"
                                name="correo"
                                value="<?php echo htmlspecialchars($cliente['correo']); ?>">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Dirección</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="direccion"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>

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