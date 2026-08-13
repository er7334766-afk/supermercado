<?php
session_start();
require_once 'config/conexion.php';

$message = '';

// Asegurarse de que las columnas para reset existan (para entornos locales)
try {
    $stmt = $conexion->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN reset_token VARCHAR(255) NULL, ADD COLUMN reset_expires DATETIME NULL");
    }
} catch (PDOException $e) {
    // ignorar, no crítico
}

// Paso 1: solicitar email para generar token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if ($email === '') {
        $message = 'Ingrese un correo válido.';
    } else {
        try {
            $stmt = $conexion->prepare('SELECT id_usuario FROM usuarios WHERE correo = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                $token = bin2hex(random_bytes(16));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
                $stmt = $conexion->prepare('UPDATE usuarios SET reset_token = ?, reset_expires = ? WHERE id_usuario = ?');
                $stmt->execute([$token, $expires, $user['id_usuario']]);
                // En un entorno real aquí se enviaría el correo con el enlace. Para pruebas locales mostramos el enlace.
                $resetLink = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/recuperar_contrasena.php?token=' . $token;
                $message = 'Se generó un enlace de recuperación. (En entorno local se muestra a continuación)';
            } else {
                $message = 'No existe una cuenta con ese correo.';
            }
        } catch (PDOException $e) {
            $message = 'Error al procesar la solicitud.';
        }
    }
}

// Paso 2: mostrar formulario de nueva contraseña cuando token está presente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['new_password'])) {
    $token = trim($_POST['token']);
    $newPassword = trim($_POST['new_password']);
    if ($token === '' || $newPassword === '') {
        $message = 'Token o contraseña inválidos.';
    } else {
        try {
            $stmt = $conexion->prepare('SELECT id_usuario, reset_expires FROM usuarios WHERE reset_token = ? LIMIT 1');
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            if ($user && strtotime($user['reset_expires']) >= time()) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare('UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id_usuario = ?');
                $stmt->execute([$hash, $user['id_usuario']]);
                $message = 'Contraseña actualizada correctamente. Puede iniciar sesión con la nueva contraseña.';
            } else {
                $message = 'Token inválido o expirado.';
            }
        } catch (PDOException $e) {
            $message = 'Error al actualizar la contraseña.';
        }
    }
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <div class="container mt-5">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Recuperar contraseña</h4>
          <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>

          <?php if (isset($resetLink)) : ?>
            <div class="alert alert-secondary">Enlace de recuperación (pruebas locales): <a href="<?php echo htmlspecialchars($resetLink); ?>"><?php echo htmlspecialchars($resetLink); ?></a></div>
          <?php endif; ?>

          <?php if (isset($_GET['token']) && $_GET['token'] !== '') : ?>
            <form method="POST" action="recuperar_contrasena.php">
              <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
              <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="new_password" class="form-control" required>
              </div>
              <button class="btn btn-primary">Actualizar contraseña</button>
            </form>
          <?php else : ?>
            <form method="POST" action="recuperar_contrasena.php">
              <div class="mb-3">
                <label class="form-label">Correo registrado</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <button class="btn btn-primary">Enviar enlace de recuperación</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </body>
</html>
