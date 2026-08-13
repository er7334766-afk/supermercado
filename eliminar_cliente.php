<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('clientes', 'eliminar');

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