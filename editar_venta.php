<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('ventas', 'editar');

$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$venta = null;
$clientes = [];
$errores = [];

try {
    $stmt = $conexion->prepare('SELECT id_venta, fk_cliente, numero_factura, fecha_venta, subtotal, descuento, impuesto, total, metodo_pago, monto_recibido, cambio, estado FROM ventas WHERE id_venta = ? LIMIT 1');
    $stmt->execute([$idVenta]);
    $venta = $stmt->fetch();
} catch (PDOException $e) {
    $errores[] = 'No se pudo cargar la venta.';
}

try {
    $stmt = $conexion->prepare('SELECT id_cliente, nombre, apellido FROM clientes WHERE estado = 1 ORDER BY nombre ASC');
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVenta = (int)($_POST['id_venta'] ?? 0);
    $fkCliente = (int)($_POST['fk_cliente'] ?? 0);
    $metodoPago = trim($_POST['metodo_pago'] ?? 'Efectivo');
    $estado = trim($_POST['estado'] ?? 'Completada');
    $descuento = (float)($_POST['descuento'] ?? 0);
    $montoRecibido = (float)($_POST['monto_recibido'] ?? 0);

    try {
        $total = (float)($_POST['total'] ?? 0);
        $cambio = round($montoRecibido - $total, 2);
        $stmt = $conexion->prepare('UPDATE ventas SET fk_cliente = ?, descuento = ?, metodo_pago = ?, monto_recibido = ?, cambio = ?, estado = ? WHERE id_venta = ?');
        $stmt->execute([$fkCliente > 0 ? $fkCliente : null, $descuento, $metodoPago, $montoRecibido, $cambio, $estado, $idVenta]);
        header('Location: ventas.php?success=1');
        exit;
    } catch (PDOException $e) {
        $errores[] = 'No se pudo actualizar la venta.';
    }
}

if (!$venta) {
    $errores[] = 'La venta solicitada no existe.';
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Venta</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>
    <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center header-title">
            <h2>Editar Venta</h2>
            <a href="ventas.php" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (!empty($errores)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errores[0]); ?></div>
                <?php endif; ?>
                <?php if ($venta) : ?>
                    <form action="editar_venta.php?id=<?php echo intval($idVenta); ?>" method="POST">
                        <input type="hidden" name="id_venta" value="<?php echo intval($venta['id_venta']); ?>">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número de Factura</label>
                                <input type="text" class="form-control" name="numero_factura" value="<?php echo htmlspecialchars($venta['numero_factura']); ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-select" name="fk_cliente">
                                    <option value="">Consumidor Final</option>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <option value="<?php echo intval($cliente['id_cliente']); ?>" <?php echo ((int)$venta['fk_cliente'] === (int)$cliente['id_cliente']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="datetime-local" class="form-control" name="fecha_venta" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($venta['fecha_venta']))); ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subtotal</label>
                                <input type="number" step="0.01" class="form-control" name="subtotal" value="<?php echo number_format((float)$venta['subtotal'], 2, '.', ''); ?>" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Descuento</label>
                                <input type="number" step="0.01" class="form-control" name="descuento" value="<?php echo number_format((float)$venta['descuento'], 2, '.', ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Impuesto</label>
                                <input type="number" step="0.01" class="form-control" name="impuesto" value="<?php echo number_format((float)$venta['impuesto'], 2, '.', ''); ?>" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total</label>
                                <input type="number" step="0.01" class="form-control" name="total" value="<?php echo number_format((float)$venta['total'], 2, '.', ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Método de Pago</label>
                                <select class="form-select" name="metodo_pago">
                                    <option value="Efectivo" <?php echo ($venta['metodo_pago'] === 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                                    <option value="Tarjeta" <?php echo ($venta['metodo_pago'] === 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
                                    <option value="Transferencia" <?php echo ($venta['metodo_pago'] === 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>
                                    <option value="Pago móvil" <?php echo ($venta['metodo_pago'] === 'Pago móvil') ? 'selected' : ''; ?>>Pago móvil</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Monto Recibido</label>
                                <input type="number" step="0.01" class="form-control" name="monto_recibido" value="<?php echo number_format((float)$venta['monto_recibido'], 2, '.', ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cambio</label>
                                <input type="number" step="0.01" class="form-control" name="cambio" value="<?php echo number_format((float)$venta['cambio'], 2, '.', ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="Completada" <?php echo ($venta['estado'] === 'Completada') ? 'selected' : ''; ?>>Completada</option>
                                <option value="Pendiente" <?php echo ($venta['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Anulada" <?php echo ($venta['estado'] === 'Anulada') ? 'selected' : ''; ?>>Anulada</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Actualizar Venta</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>