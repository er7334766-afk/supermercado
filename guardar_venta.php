<?php
require_once 'config/conexion.php';

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

$descuento = 0.0;
$impuesto = round($subtotal * 0.15, 2);
$total = round($subtotal + $impuesto, 2);
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

    $conexion->commit();
    header('Location: ventas.php?success=1');
    exit;
} catch (PDOException $e) {
    $conexion->rollBack();
    header('Location: nueva_venta.php?error=' . urlencode($e->getMessage()));
    exit;
}
