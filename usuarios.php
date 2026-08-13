<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Control de acceso por rol: solo administradores (rol 1) pueden ver esta página
if (!isset($_SESSION['usuario_rol']) || (int)$_SESSION['usuario_rol'] !== 1) {
  header('Location: inicio.php?error=' . urlencode('Acceso denegado'));
  exit;
}

require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.usuario, u.telefono, u.estado, r.nombre AS rol FROM usuarios u LEFT JOIN roles r ON r.id_rol = u.fk_rol ORDER BY u.id_usuario DESC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    $usuarios = [];
    $error = 'No se pudieron cargar los usuarios: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Usuarios</h2>
        <a href="nuevo_usuario.php" class="btn btn-primary">Nuevo usuario</a>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($usuarios)) : ?>
                <?php foreach ($usuarios as $usuario) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol'] ?? 'Sin rol'); ?></td>
                    <td>
                      <?php if (!empty($usuario['estado'])) : ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else : ?>
                        <span class="badge bg-secondary">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="editar_usuario.php?id=<?php echo intval($usuario['id_usuario']); ?>" class="btn btn-sm btn-warning">Editar</a>
                      <a href="eliminar_usuario.php?id=<?php echo intval($usuario['id_usuario']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este usuario?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="7" class="text-center text-muted">No hay usuarios registrados.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>
