<?php
session_start();

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';
require_once 'config/auditoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: caja.php');
    exit;
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('caja', 'crear');

$fk_usuario = (int)$_SESSION['usuario_id'];
$nombre = trim((string)($_POST['nombre'] ?? ''));
$ubicacion = trim((string)($_POST['ubicacion'] ?? ''));
$estado = trim((string)($_POST['estado'] ?? 'Cerrada'));

if ($nombre === '') {
    header('Location: nueva_caja.php?error=' . urlencode('El nombre es requerido'));
    exit;
}

try {
    $stmt = $conexion->prepare('INSERT INTO cajas (nombre, ubicacion, estado) VALUES (?, ?, ?)');
    $stmt->execute([$nombre, $ubicacion !== '' ? $ubicacion : null, $estado]);
    $id_caja = (int)$conexion->lastInsertId();

    // Registrar auditoría
    try {
        $detalle = 'Caja creada: ' . $nombre . ' (id=' . $id_caja . ')';
        registrarAuditoria($conexion, $fk_usuario, 'Crear caja', $detalle, 'caja', 'cajas', $id_caja);
    } catch (Throwable $e) {
        error_log('Error auditoría crear caja: ' . $e->getMessage());
    }

    header('Location: caja.php?success=1');
    exit;
} catch (PDOException $e) {
    $msg = $e->getMessage();
    header('Location: nueva_caja.php?error=' . urlencode($msg));
    exit;
}
