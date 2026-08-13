<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/auditoria.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: inicio.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '' && $password !== '') {
        try {
            $stmt = $conexion->prepare('SELECT id_usuario, usuario, password, nombre, apellido, fk_rol FROM usuarios WHERE usuario = ? AND estado = 1 LIMIT 1');
            $stmt->execute([$usuario]);
            $user = $stmt->fetch();

            $passwordOk = false;
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $passwordOk = true;
                } elseif ($user['password'] === $password) {
                    $passwordOk = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUpdate = $conexion->prepare('UPDATE usuarios SET password = ? WHERE id_usuario = ?');
                    $stmtUpdate->execute([$newHash, $user['id_usuario']]);
                }
            }

            if ($passwordOk) {
                $_SESSION['usuario_id'] = (int)$user['id_usuario'];
                $_SESSION['usuario_nombre'] = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
                $_SESSION['usuario_usuario'] = $user['usuario'];
                $_SESSION['usuario_rol'] = (int)$user['fk_rol'];
              // Registrar en auditoría el ingreso exitoso
              try {
                registrarAuditoria($conexion, (int)$user['id_usuario'], 'Inicio de sesión', 'Usuario inició sesión correctamente', 'auth', null, null);
              } catch (Throwable $e) {
                error_log('Error registrando auditoría de login: ' . $e->getMessage());
              }

              header('Location: inicio.php');
              exit;
            }

            $error = 'Usuario o contraseña incorrecta.';
        } catch (PDOException $e) {
            $error = 'No se pudo iniciar sesión.';
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <div class="container login-wrapper">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-center">Iniciar sesion</h3>
          <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <form method="POST" action="index.php">
            <div class="mb-3">
              <label class="form-label">Usuario</label>
              <input type="text" class="form-control" name="usuario" placeholder="usuario" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
          </form>
          <div class="text-center mt-3">
            <small>
              <a href="recuperar_contrasena.php" class="text-muted text-decoration-none">¿Olvidaste tu contraseña?</a>
            </small>
          </div>
        </div>
      </div>
    </div>
    <script src="static/js/scripts.js"></script>
</body>
</html>



