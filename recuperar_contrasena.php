<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/mailer.php';

$message = '';
$messageType = ''; // 'success', 'error', 'info'
$resetLink = null;
$step = 1; // 1: solicitar email, 2: cambiar contraseña
$showResetForm = false;
$tokenValido = false;

// Si ya está autenticado, redirigir a inicio
if (isset($_SESSION['usuario_id'])) {
    header('Location: inicio.php');
    exit;
}

// PASO 1: Usuario solicita recuperación (ingresa correo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '1') {
    $email = trim($_POST['email'] ?? '');
    
    // Validar que el email no esté vacío
    if ($email === '') {
        $message = 'Por favor ingresa tu correo electrónico.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Por favor ingresa un correo válido.';
        $messageType = 'error';
    } else {
        try {
            // Buscar usuario por correo
            $stmt = $conexion->prepare('SELECT id_usuario, nombre, correo FROM usuarios WHERE correo = ? LIMIT 1');
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario) {
                // Generar token seguro
                $token = bin2hex(random_bytes(32)); // 64 caracteres
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
                
                // Guardar token en la BD
                $stmtUpdate = $conexion->prepare('UPDATE usuarios SET reset_token = ?, reset_expires = ? WHERE id_usuario = ?');
                $stmtUpdate->execute([$token, $expires, $usuario['id_usuario']]);
                
                // Enviar correo con enlace
                $nombreUsuario = $usuario['nombre'] ?? 'Usuario';
                if (enviarCorreoRecuperacion($email, $nombreUsuario, $token)) {
                    $message = 'Se ha enviado un enlace de recuperación a tu correo. Por favor revisa tu bandeja de entrada y sigue las instrucciones.';
                    $messageType = 'success';
                } else {
                    // Si falla el envío, mostrar el enlace para desarrollo local
                    $resetLink = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/recuperar_contrasena.php?token=' . $token;
                    $message = '<strong>Nota para desarrollo:</strong> El correo no se envió (configura SMTP en config/mailer.php). Usa este enlace para pruebas: ' . $resetLink;
                    $messageType = 'info';
                }
            } else {
                // Mensaje de seguridad: no revelar si el email existe o no
                $message = 'Si existe una cuenta con ese correo, recibirás un enlace de recuperación.';
                $messageType = 'info';
            }
        } catch (PDOException $e) {
            $message = 'Ocurrió un error al procesar tu solicitud. Por favor intenta más tarde.';
            $messageType = 'error';
            error_log('Error BD recuperación contraseña: ' . $e->getMessage());
        }
    }
}

// PASO 2: Usuario accede con token y cambia contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '2') {
    $token = trim($_POST['token'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    // Validaciones
    if ($token === '') {
        $message = 'Token inválido.';
        $messageType = 'error';
    } elseif ($newPassword === '' || $confirmPassword === '') {
        $message = 'Por favor ingresa y confirma tu nueva contraseña.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Las contraseñas no coinciden. Por favor intenta de nuevo.';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
        $messageType = 'error';
    } else {
        try {
            // Buscar usuario por token
            $stmt = $conexion->prepare('SELECT id_usuario, reset_expires FROM usuarios WHERE reset_token = ? LIMIT 1');
            $stmt->execute([$token]);
            $usuario = $stmt->fetch();
            
            // Validar que el usuario exista y el token no haya expirado
            if ($usuario) {
                // Comparar timestamps correctamente
                $ahora = new DateTime();
                $expira = new DateTime($usuario['reset_expires']);
                
                if ($ahora <= $expira) {
                    // Token válido, actualizar contraseña y limpiar token
                    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmtUpdate = $conexion->prepare('UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id_usuario = ?');
                    $stmtUpdate->execute([$hash, $usuario['id_usuario']]);
                    
                    $message = 'Tu contraseña ha sido actualizada correctamente. Ahora puedes <a href="index.php">iniciar sesión</a> con tu nueva contraseña.';
                    $messageType = 'success';
                    $showResetForm = false;
                } else {
                    $message = 'El enlace de recuperación ha expirado. Por favor solicita uno nuevo.';
                    $messageType = 'error';
                    $showResetForm = false;
                }
            } else {
                $message = 'El token no es válido. Por favor solicita un nuevo enlace de recuperación.';
                $messageType = 'error';
                $showResetForm = false;
            }
        } catch (PDOException $e) {
            $message = 'Ocurrió un error al actualizar tu contraseña. Por favor intenta más tarde.';
            $messageType = 'error';
            error_log('Error BD actualizar contraseña: ' . $e->getMessage());
        }
    }
}

// Verificar si hay token en la URL
$tokenFromUrl = $_GET['token'] ?? '';
if ($tokenFromUrl !== '') {
    try {
        $stmt = $conexion->prepare('SELECT id_usuario, reset_expires FROM usuarios WHERE reset_token = ? LIMIT 1');
        $stmt->execute([$tokenFromUrl]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            $ahora = new DateTime();
            $expira = new DateTime($usuario['reset_expires']);
            
            if ($ahora <= $expira) {
                $showResetForm = true;
                $tokenValido = true;
            } else {
                $message = 'El enlace de recuperación ha expirado. Por favor solicita uno nuevo.';
                $messageType = 'error';
            }
        } else {
            $message = 'El enlace no es válido. Por favor solicita un nuevo enlace de recuperación.';
            $messageType = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Error al validar el enlace.';
        $messageType = 'error';
    }
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña - Sistema Supermercado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
    <style>
      .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
      .alert-error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
      .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
      .alert a { color: inherit; text-decoration: underline; }
      .recovery-wrapper { max-width: 450px; margin: 60px auto; }
    </style>
  </head>
  <body>
    <div class="container recovery-wrapper">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">
            <?php echo $showResetForm ? 'Nueva Contraseña' : 'Recuperar Contraseña'; ?>
          </h3>

          <?php if (!empty($message)) : ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
              <?php echo $message; ?>
            </div>
          <?php endif; ?>

          <?php if ($showResetForm && $tokenValido) : ?>
            <!-- FORMULARIO: Cambiar contraseña -->
            <form method="POST" action="recuperar_contrasena.php" novalidate>
              <input type="hidden" name="step" value="2">
              <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenFromUrl); ?>">
              
              <div class="mb-3">
                <label class="form-label">Nueva Contraseña</label>
                <input 
                  type="password" 
                  name="new_password" 
                  class="form-control" 
                  placeholder="Ingresa tu nueva contraseña"
                  minlength="6"
                  required>
                <small class="form-text text-muted">Mínimo 6 caracteres</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Confirmar Contraseña</label>
                <input 
                  type="password" 
                  name="confirm_password" 
                  class="form-control" 
                  placeholder="Confirma tu nueva contraseña"
                  minlength="6"
                  required>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
              </div>
            </form>

          <?php else : ?>
            <!-- FORMULARIO: Solicitar recuperación (correo) -->
            <form method="POST" action="recuperar_contrasena.php" novalidate>
              <input type="hidden" name="step" value="1">
              
              <p class="text-muted small mb-3">
                Ingresa tu correo electrónico registrado y te enviaremos un enlace para recuperar tu contraseña.
              </p>

              <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input 
                  type="email" 
                  name="email" 
                  class="form-control" 
                  placeholder="tu-email@ejemplo.com"
                  required>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Enviar Enlace de Recuperación</button>
                <a href="index.php" class="btn btn-secondary">Volver al Login</a>
              </div>
            </form>

          <?php endif; ?>

          <?php if (!empty($resetLink)) : ?>
            <div class="alert alert-secondary mt-3">
              <strong>Para desarrollo local:</strong><br>
              <a href="<?php echo htmlspecialchars($resetLink); ?>" class="text-break" style="word-break: break-all;">
                <?php echo htmlspecialchars($resetLink); ?>
              </a>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <div class="text-center mt-4">
        <small class="text-muted">
          ¿Ya recuerdas tu contraseña? <a href="index.php">Inicia sesión aquí</a>
        </small>
      </div>
    </div>

    <script src="static/js/scripts.js"></script>
  </body>
</html>
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
