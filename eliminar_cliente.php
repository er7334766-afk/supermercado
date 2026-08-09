<?php
require_once 'config/conexion.php';

$id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_cliente > 0) {
    try {
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
    } catch (PDOException $e) {
        $error = 'No se pudo eliminar el cliente: ' . $e->getMessage();
    }
}

header('Location: clientes.php');
exit;