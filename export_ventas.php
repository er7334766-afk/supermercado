<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('ventas');

try {
    $stmt = $conexion->prepare("SELECT v.id_venta, v.numero_factura, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, v.subtotal, v.descuento, v.impuesto, v.total, v.metodo_pago, v.fecha_venta, v.estado FROM ventas v LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente ORDER BY v.id_venta DESC");
    $stmt->execute();
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ventas = [];
}

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="ventas_export.csv"');
echo "F"; // BOM may help Excel recognize UTF-8
// cabecera
echo "ID,Factura,Cliente,Subtotal,Descuento,Impuesto,Total,MetodoPago,Fecha,Estado\n";
foreach ($ventas as $v) {
    $cliente = trim(($v['cliente_nombre'] ?? '') . ' ' . ($v['cliente_apellido'] ?? ''));
    $line = [
        $v['id_venta'],
        $v['numero_factura'],
        str_replace(',', ' ', $cliente),
        number_format((float)$v['subtotal'], 2, '.', ''),
        number_format((float)$v['descuento'], 2, '.', ''),
        number_format((float)$v['impuesto'], 2, '.', ''),
        number_format((float)$v['total'], 2, '.', ''),
        $v['metodo_pago'],
        $v['fecha_venta'],
        $v['estado']
    ];
    echo implode(',', $line) . "\n";
}
exit;
