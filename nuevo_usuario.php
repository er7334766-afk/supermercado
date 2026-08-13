<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('usuarios', 'crear');

try {
    $stmt = $conexion->prepare("SELECT id_rol, nombre FROM roles WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    $roles = [];
    $error = 'No se pudieron cargar los roles: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fk_rol = (int)($_POST['fk_rol'] ?? 0);

    if ($nombre !== '' && $apellido !== '' && $correo !== '' && $usuario !== '' && $password !== '' && $fk_rol > 0) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (fk_rol, nombre, apellido, correo, usuario, password, telefono, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$fk_rol, $nombre, $apellido, $correo, $usuario, $hash, $telefono]);
            header('Location: usuarios.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo guardar el usuario: ' . $e->getMessage();
        }
    } else {
        $error = 'Completa los campos obligatorios.';
    }
}
?>
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
        <a href="usuarios.php" class="btn btn-secondary">Volver</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="nuevo_usuario.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" class="form-control" name="usuario" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="fk_rol" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($roles as $rol) : ?>
                                <option value="<?php echo intval($rol['id_rol']); ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/js/scripts.js"></script>
</body>
</html>