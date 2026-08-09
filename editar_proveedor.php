<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

$proveedor = [
    'id_proveedor' => 0,
    'nombre_empresa' => '',
    'contacto' => '',
    'rtn' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => '',
    'estado' => 1
];

$id_proveedor = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
    $proveedor['nombre_empresa'] = trim($_POST['nombre_empresa'] ?? '');
    $proveedor['contacto'] = trim($_POST['contacto'] ?? '');
    $proveedor['rtn'] = trim($_POST['rtn'] ?? '');
    $proveedor['telefono'] = trim($_POST['telefono'] ?? '');
    $proveedor['correo'] = trim($_POST['correo'] ?? '');
    $proveedor['direccion'] = trim($_POST['direccion'] ?? '');
    $proveedor['estado'] = (int)($_POST['estado'] ?? 1);

    if ($id_proveedor > 0 && $proveedor['nombre_empresa'] !== '') {
        try {
            $stmt = $conexion->prepare("UPDATE proveedores SET nombre_empresa = ?, contacto = ?, rtn = ?, telefono = ?, correo = ?, direccion = ?, estado = ? WHERE id_proveedor = ?");
            $stmt->execute([$proveedor['nombre_empresa'], $proveedor['contacto'], $proveedor['rtn'], $proveedor['telefono'], $proveedor['correo'], $proveedor['direccion'], $proveedor['estado'], $id_proveedor]);
            header('Location: proveedores.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo actualizar el proveedor: ' . $e->getMessage();
        }
    } else {
        $error = 'El nombre de la empresa es obligatorio.';
    }
}

if ($id_proveedor > 0) {
    try {
        $stmt = $conexion->prepare("SELECT id_proveedor, nombre_empresa, contacto, rtn, telefono, correo, direccion, estado FROM proveedores WHERE id_proveedor = ?");
        $stmt->execute([$id_proveedor]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC) ?: $proveedor;
    } catch (PDOException $e) {
        $error = 'No se pudo cargar el proveedor: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Editar Proveedor</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Proveedor</h2>

            <a href="proveedores.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="editar_proveedor.php" method="POST">

                    <input type="hidden" name="id_proveedor" value="<?php echo intval($proveedor['id_proveedor']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre de la Empresa</label>
                        <input type="text" class="form-control" name="nombre_empresa" value="<?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Persona de Contacto</label>
                        <input type="text" class="form-control" name="contacto" value="<?php echo htmlspecialchars($proveedor['contacto']); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RTN</label>
                            <input type="text" class="form-control" name="rtn" value="<?php echo htmlspecialchars($proveedor['rtn']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($proveedor['correo']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" rows="3" name="direccion"><?php echo htmlspecialchars($proveedor['direccion']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="1" <?php echo ((int)$proveedor['estado'] === 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ((int)$proveedor['estado'] === 0) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Actualizar Proveedor</button>
                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>

    </body>
</html>