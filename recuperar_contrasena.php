<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/auditoria.php';

// Objetivo: Simplificar la recuperación/cambio de contraseña
// Reglas:
// - Si el usuario está autenticado (`$_SESSION['usuario_id']`) sólo pide la contraseña anterior y la nueva.
// - Si NO está autenticado, se pide el correo registrado + contraseña anterior + nueva.
// - Si la contraseña anterior coincide, se actualiza por la nueva (hasheada).

$message = '';
$messageType = ''; // 'success' | 'error'

// Si ya está autenticado, permitimos cambiar con la contraseña anterior
$isLogged = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $oldPassword = trim($_POST['old_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($isLogged) {
        $userId = intval($_SESSION['usuario_id']);
        $userStmt = $conexion->prepare('SELECT id_usuario, password, correo FROM usuarios WHERE id_usuario = ? LIMIT 1');
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();
    } else {
        $email = trim($_POST['email'] ?? '');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Por favor ingresa un correo válido.';
            $messageType = 'error';
            $user = false;
        } else {
            $userStmt = $conexion->prepare('SELECT id_usuario, password, correo FROM usuarios WHERE correo = ? LIMIT 1');
            $userStmt->execute([$email]);
            $user = $userStmt->fetch();
        }
    }

    // Validaciones de formulario
    if (empty($user)) {
        if ($message === '') {
            $message = 'Usuario no encontrado.';
            $messageType = 'error';
        }
    } elseif ($oldPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $message = 'Por favor completa todos los campos.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'La nueva contraseña y la confirmación no coinciden.';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
        $messageType = 'error';
    } else {
        // Verificar contraseña anterior
        if (!password_verify($oldPassword, $user['password'])) {
            $message = 'La contraseña anterior es incorrecta.';
            $messageType = 'error';
        } else {
            // Actualizar contraseña
            try {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $conexion->prepare('UPDATE usuarios SET password = ? WHERE id_usuario = ?');
                $update->execute([$hash, $user['id_usuario']]);

                $message = 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.';
                $messageType = 'success';

                // Registrar auditoría: cambio de contraseña
                try {
                  $fk_usuario_aud = $isLogged ? intval($_SESSION['usuario_id']) : intval($user['id_usuario']);
                  $detalle = 'Cambio de contraseña para usuario ' . ($user['correo'] ?? $fk_usuario_aud);
                  registrarAuditoria($conexion, $fk_usuario_aud, 'Cambio de contraseña', $detalle, 'auth', 'usuarios', $user['id_usuario']);
                } catch (Throwable $ta) {
                  // No bloquear al usuario si falla la auditoría
                  error_log('Error registrando auditoría cambio contraseña: ' . $ta->getMessage());
                }
            } catch (PDOException $e) {
                error_log('Error actualizando contraseña: ' . $e->getMessage());
                $message = 'Ocurrió un error al actualizar la contraseña. Intenta más tarde.';
                $messageType = 'error';
            }
        }
    }
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
    <style>
      .change-wrapper { max-width: 480px; margin: 60px auto; }
    </style>
  </head>
  <body>
    <div class="container change-wrapper">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">Cambiar Contraseña</h3>

          <?php if ($message !== '') : ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>" role="alert">
              <?php echo $message; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="recuperar_contrasena.php" novalidate>
            <input type="hidden" name="action" value="change_password">

            <?php if (!$isLogged) : ?>
              <div class="mb-3">
                <label class="form-label">Correo electrónico registrado</label>
                <input type="email" name="email" class="form-control" placeholder="tu-email@ejemplo.com" required>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Contraseña anterior</label>
              <input type="password" name="old_password" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Nueva contraseña</label>
              <input type="password" name="new_password" class="form-control" minlength="6" required>
              <div class="form-text">Mínimo 6 caracteres</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirmar nueva contraseña</label>
              <input type="password" name="confirm_password" class="form-control" minlength="6" required>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
              <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
          </form>
        </div>
      </div>

      <div class="text-center mt-4">
        <small class="text-muted">Si no recuerdas tu contraseña anterior, contacta al administrador.</small>
      </div>
    </div>

    <script src="static/js/scripts.js"></script>
  </body>
</html>
