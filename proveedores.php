<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT id_proveedor, nombre_empresa, contacto, rtn, telefono, correo, direccion, estado FROM proveedores ORDER BY id_proveedor DESC");
    $stmt->execute();
    $proveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $proveedores = [];
    $error = 'No se pudieron cargar los proveedores: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proveedores - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Proveedores</h2>
        <a href="nuevo_proveedor.php" class="btn btn-primary">Nuevo proveedor</a>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Empresa</th>
                <th>Contacto</th>
                <th>RTN</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($proveedores)) : ?>
                <?php foreach ($proveedores as $proveedor) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['contacto'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['rtn'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['telefono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['correo'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['direccion'] ?? ''); ?></td>
                    <td>
                      <?php if (!empty($proveedor['estado'])) : ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else : ?>
                        <span class="badge bg-secondary">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="editar_proveedor.php?id=<?php echo intval($proveedor['id_proveedor']); ?>" class="btn btn-sm btn-warning">Editar</a>
                      <a href="eliminar_proveedor.php?id=<?php echo intval($proveedor['id_proveedor']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este proveedor?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="8" class="text-center text-muted">No hay proveedores registrados.</td>
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
