<?php
require_once 'config/conexion.php';

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
