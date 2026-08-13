<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: compras.php');
    exit;
}

requerirAccesoAccion('compras', 'crear');

$fk_usuario = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 1;
$fk_proveedor = isset($_POST['fk_proveedor']) ? (int)$_POST['fk_proveedor'] : 0;
$numero_factura = trim((string)($_POST['numero_factura'] ?? ''));
$fecha_compra = trim((string)($_POST['fecha_compra'] ?? date('Y-m-d H:i:s')));
$estado = trim((string)($_POST['estado'] ?? 'Registrada'));
$productos = $_POST['producto'] ?? [];
$precios = $_POST['precio_compra'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if ($numero_factura === '') {
    $numero_factura = 'COMP-' . date('YmdHis');
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

$impuesto = round($subtotal * 0.15, 2);
$total = round($subtotal + $impuesto, 2);

try {
    $conexion->beginTransaction();

    $stmt = $conexion->prepare(
        'INSERT INTO compras (fk_proveedor, fk_usuario, numero_factura, fecha_compra, subtotal, impuesto, total, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)' 
    );
    $stmt->execute([
        $fk_proveedor > 0 ? $fk_proveedor : null,
        $fk_usuario,
        $numero_factura,
        $fecha_compra,
        $subtotal,
        $impuesto,
        $total,
        $estado
    ]);
    $compraId = (int)$conexion->lastInsertId();

    $stmtDetalle = $conexion->prepare(
        'INSERT INTO detalle_compras (fk_compra, fk_producto, cantidad, precio_compra, subtotal) VALUES (?, ?, ?, ?, ?)' 
    );
    foreach ($detalle as $item) {
        $stmtDetalle->execute([
            $compraId,
            $item['producto_id'],
            $item['cantidad'],
            $item['precio'],
            $item['linea_subtotal']
        ]);
    }

    $conexion->commit();
    header('Location: compras.php?success=1');
    exit;
} catch (PDOException $e) {
    $conexion->rollBack();
    header('Location: nueva_compra.php?error=' . urlencode($e->getMessage()));
    exit;
}
