<?php
session_start();

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/auditoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: caja.php');
    exit;
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('caja');

$fk_usuario = (int)$_SESSION['usuario_id'];
$fk_caja = isset($_POST['fk_caja']) ? (int)$_POST['fk_caja'] : 0;
$monto_inicial = isset($_POST['monto_inicial']) ? (float)$_POST['monto_inicial'] : 0.0;

if ($fk_caja <= 0) {
    header('Location: nueva_apertura.php?error=' . urlencode('Seleccione una caja.'));
    exit;
}

try {
    $conexion->beginTransaction();

    $stmt = $conexion->prepare('INSERT INTO aperturas_caja (fk_caja, fk_usuario, monto_inicial, monto_contado, estado, fecha_apertura) VALUES (?, ?, ?, ?, ?, NOW())');
    $estado = 'Abierta';
    $stmt->execute([$fk_caja, $fk_usuario, $monto_inicial, $monto_inicial, $estado]);

    // Actualizar estado de la caja
    $stmt2 = $conexion->prepare('UPDATE cajas SET estado = ? WHERE id_caja = ?');
    $stmt2->execute([$estado, $fk_caja]);

    $id_apertura = (int)$conexion->lastInsertId();

    // Registrar auditoría
    try {
        $detalle = 'Apertura #' . $id_apertura . ', caja: ' . $fk_caja . ', monto_inicial: L. ' . number_format($monto_inicial, 2, '.', ',');
        registrarAuditoria($conexion, $fk_usuario, 'Abrir caja', $detalle);
    } catch (Throwable $e) {
        error_log('Error auditoría apertura: ' . $e->getMessage());
    }

    $conexion->commit();

    header('Location: caja.php?success=1');
    exit;

} catch (Throwable $e) {
    if ($conexion->inTransaction()) $conexion->rollBack();
    header('Location: nueva_apertura.php?error=' . urlencode($e->getMessage()));
    exit;
}
