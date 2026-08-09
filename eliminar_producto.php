<?php
require_once 'config/conexion.php';

$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_producto > 0) {
    try {
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
    } catch (PDOException $e) {
        $error = 'No se pudo eliminar el producto: ' . $e->getMessage();
    }
}

header('Location: productos.php');
exit;
