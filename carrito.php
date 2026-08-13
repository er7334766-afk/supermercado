<?php
session_start();
require_once 'config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$userId = (int)$_SESSION['usuario_id'];

try {
    // asegurarse de que la tabla exista
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS cart_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fk_usuario INT NOT NULL,
            fk_producto INT NOT NULL,
            cantidad INT NOT NULL DEFAULT 1,
            precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (fk_usuario),
            INDEX (fk_producto)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
} catch (PDOException $e) {
    // continue even if create fails
}

$action = $_REQUEST['action'] ?? 'list';
try {
    if ($action === 'add') {
        $producto = (int)($_POST['producto'] ?? 0);
        $cantidad = max(1, (int)($_POST['cantidad'] ?? 1));
        if ($producto <= 0) throw new Exception('Producto inválido');
        // obtener precio actual
        $stmt = $conexion->prepare('SELECT precio_venta FROM productos WHERE id_producto = ? LIMIT 1');
        $stmt->execute([$producto]);
        $p = $stmt->fetch();
        $precio = $p ? (float)$p['precio_venta'] : 0;

        // si ya existe, sumar cantidad
        $stmt = $conexion->prepare('SELECT id, cantidad FROM cart_items WHERE fk_usuario = ? AND fk_producto = ? LIMIT 1');
        $stmt->execute([$userId, $producto]);
        $exists = $stmt->fetch();
        if ($exists) {
            $newQty = (int)$exists['cantidad'] + $cantidad;
            $stmt = $conexion->prepare('UPDATE cart_items SET cantidad = ?, precio_unitario = ? WHERE id = ?');
            $stmt->execute([$newQty, $precio, $exists['id']]);
        } else {
            $stmt = $conexion->prepare('INSERT INTO cart_items (fk_usuario, fk_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
            $stmt->execute([$userId, $producto, $cantidad, $precio]);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'update') {
        $producto = (int)($_POST['producto'] ?? 0);
        $cantidad = max(0, (int)($_POST['cantidad'] ?? 0));
        if ($producto <= 0) throw new Exception('Producto inválido');
        if ($cantidad === 0) {
            $stmt = $conexion->prepare('DELETE FROM cart_items WHERE fk_usuario = ? AND fk_producto = ?');
            $stmt->execute([$userId, $producto]);
        } else {
            $stmt = $conexion->prepare('UPDATE cart_items ci JOIN productos p ON p.id_producto = ci.fk_producto SET ci.cantidad = ?, ci.precio_unitario = p.precio_venta WHERE ci.fk_usuario = ? AND ci.fk_producto = ?');
            $stmt->execute([$cantidad, $userId, $producto]);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'remove') {
        $producto = (int)($_POST['producto'] ?? 0);
        if ($producto <= 0) throw new Exception('Producto inválido');
        $stmt = $conexion->prepare('DELETE FROM cart_items WHERE fk_usuario = ? AND fk_producto = ?');
        $stmt->execute([$userId, $producto]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'clear') {
        $stmt = $conexion->prepare('DELETE FROM cart_items WHERE fk_usuario = ?');
        $stmt->execute([$userId]);
        echo json_encode(['success' => true]);
        exit;
    }

    // default: list
    $stmt = $conexion->prepare('SELECT ci.fk_producto, ci.cantidad, ci.precio_unitario, p.nombre FROM cart_items ci LEFT JOIN productos p ON p.id_producto = ci.fk_producto WHERE ci.fk_usuario = ?');
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll();
    echo json_encode(['success' => true, 'items' => $items]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

?>
