<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('productos', 'eliminar');

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
