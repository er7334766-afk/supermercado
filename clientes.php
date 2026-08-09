<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT id_cliente, nombre, apellido, identidad, rtn, telefono, correo, direccion, estado FROM clientes ORDER BY id_cliente DESC");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
    $error = 'No se pudieron cargar los clientes: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Clientes</h2>
        <a href="nuevo_cliente.php" class="btn btn-primary"> Nuevo cliente </a>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Identidad</th>
                <th>RTN</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($clientes)) : ?>
                <?php foreach ($clientes as $cliente) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['apellido'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['identidad'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['rtn'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['correo'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></td>
                    <td>
                      <?php if (!empty($cliente['estado'])) : ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else : ?>
                        <span class="badge bg-secondary">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="editar_cliente.php?id=<?php echo intval($cliente['id_cliente']); ?>" class="btn btn-sm btn-warning">Editar</a>
                      <a href="eliminar_cliente.php?id=<?php echo intval($cliente['id_cliente']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este cliente?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="9" class="text-center text-muted">No hay clientes registrados.</td>
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

