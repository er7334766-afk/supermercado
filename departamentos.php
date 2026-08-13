<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAcceso('departamentos');

try {
    $stmt = $conexion->prepare("SELECT id_departamento, nombre, descripcion, estado FROM departamentos ORDER BY id_departamento DESC");
    $stmt->execute();
    $departamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $departamentos = [];
    $error = 'No se pudieron cargar los departamentos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Departamentos - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="d-flex justify-content-between align-items-center header-title">
        <h2>Departamentos</h2>
        <a href="nuevo_departamento.php" class="btn btn-primary"> Nuevo departamento </a>
      </div>

      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
       
          <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($departamentos)) : ?>

                    <?php foreach ($departamentos as $departamento) : ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($departamento['nombre']); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($departamento['descripcion'] ?? ''); ?>
                            </td>

                            <td>
                                <?php if (!empty($departamento['estado'])) : ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else : ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">

                                <?php if (verificarAcceso('departamentos', 'editar')) : ?>

                                    <a
                                        href="editar_departamento.php?id=<?php echo intval($departamento['id_departamento']); ?>"
                                        class="btn btn-warning btn-sm">

                                        Editar

                                    </a>

                                <?php endif; ?>


                                <?php if (verificarAcceso('departamentos', 'eliminar')) : ?>

                                    <a
                                        href="eliminar_departamento.php?id=<?php echo intval($departamento['id_departamento']); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Está seguro de eliminar este departamento?');">

                                        Eliminar

                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay departamentos registrados.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>
        </div>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>



