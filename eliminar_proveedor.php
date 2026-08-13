<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('proveedores', 'eliminar');

$id_proveedor = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_proveedor > 0) {
    try {
        $stmt = $conexion->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
        $stmt->execute([$id_proveedor]);
    } catch (PDOException $e) {
        $error = 'No se pudo eliminar el proveedor: ' . $e->getMessage();
    }
}

header('Location: proveedores.php');
exit;
