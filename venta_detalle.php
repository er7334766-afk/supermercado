<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de venta inválido']);
    exit;
}

try {
    $stmt = $conexion->prepare('SELECT dv.fk_producto, dv.cantidad, dv.precio_unitario, p.nombre FROM detalle_ventas dv LEFT JOIN productos p ON p.id_producto = dv.fk_producto WHERE dv.fk_venta = ?');
    $stmt->execute([$id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'items' => $items]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
