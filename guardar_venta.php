<?php
require_once 'config/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ventas.php');
    exit;
}

$fk_cliente = isset($_POST['fk_cliente']) ? (int)$_POST['fk_cliente'] : 0;
$numero_factura = trim((string)($_POST['numero_factura'] ?? ''));
$fecha_venta = trim((string)($_POST['fecha_venta'] ?? date('Y-m-d H:i:s')));
$metodo_pago = trim((string)($_POST['metodo_pago'] ?? 'Efectivo'));
$monto_recibido = (float)($_POST['monto_recibido'] ?? 0);
$productos = $_POST['producto'] ?? [];
$precios = $_POST['precio'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

// Si no se enviaron productos en el formulario, intentar tomar los items del carrito en BD
if (empty($productos) && !empty($_SESSION['usuario_id'])) {
    $stmtCart = $conexion->prepare('SELECT fk_producto, cantidad, precio_unitario FROM cart_items WHERE fk_usuario = ?');
    $stmtCart->execute([(int)$_SESSION['usuario_id']]);
    $cartItems = $stmtCart->fetchAll();
    if (!empty($cartItems)) {
        $productos = [];
        $precios = [];
        $cantidades = [];
        foreach ($cartItems as $ci) {
            $productos[] = (int)$ci['fk_producto'];
            $precios[] = (float)$ci['precio_unitario'];
            $cantidades[] = (int)$ci['cantidad'];
        }
    }
}

if ($numero_factura === '') {
    $numero_factura = 'FAC-' . date('YmdHis');
}

$subtotal = 0.0;
$detalle = [];
for ($i = 0; $i < count($productos); $i++) {
    $productoId = (int)($productos[$i] ?? 0);
    $precio = (float)($precios[$i] ?? 0);
    $cantidad = (int)($cantidades[$i] ?? 0);
    if ($productoId <= 0 || $cantidad <= 0) {
        continue;
    }
    $lineaSubtotal = $precio * $cantidad;
    $subtotal += $lineaSubtotal;
    $detalle[] = [
        'producto_id' => $productoId,
        'precio' => $precio,
        'cantidad' => $cantidad,
        'linea_subtotal' => $lineaSubtotal,
    ];
}

$descuento = (float)($_POST['descuento'] ?? 0.0);
$impuesto = round($subtotal * 0.15, 2);
$total = round($subtotal - $descuento + $impuesto, 2);
$cambio = round($monto_recibido - $total, 2);

try {
    $conexion->beginTransaction();

    $stmt = $conexion->prepare(
        'INSERT INTO ventas (fk_cliente, fk_usuario, fk_apertura, numero_factura, fecha_venta, subtotal, descuento, impuesto, total, metodo_pago, monto_recibido, cambio, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)' 
    );
    $stmt->execute([
        $fk_cliente > 0 ? $fk_cliente : null,
        1,
        1,
        $numero_factura,
        $fecha_venta,
        $subtotal,
        $descuento,
        $impuesto,
        $total,
        $metodo_pago,
        $monto_recibido,
        $cambio,
        'Completada'
    ]);
    $ventaId = (int)$conexion->lastInsertId();

    $stmtDetalle = $conexion->prepare(
        'INSERT INTO detalle_ventas (fk_venta, fk_producto, cantidad, precio_unitario, descuento, subtotal) VALUES (?, ?, ?, ?, ?, ?)' 
    );
    foreach ($detalle as $item) {
        $stmtDetalle->execute([
            $ventaId,
            $item['producto_id'],
            $item['cantidad'],
            $item['precio'],
            0,
            $item['linea_subtotal']
        ]);
    }

    // si usamos carrito, limpiarlo para el usuario
    if (!empty($_SESSION['usuario_id'])) {
        $stmtClear = $conexion->prepare('DELETE FROM cart_items WHERE fk_usuario = ?');
        $stmtClear->execute([(int)$_SESSION['usuario_id']]);
    }

    $conexion->commit();
    header('Location: ventas.php?success=1');
    exit;
} catch (PDOException $e) {
    $conexion->rollBack();
    header('Location: nueva_venta.php?error=' . urlencode($e->getMessage()));
    exit;
}
