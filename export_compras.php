<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('compras');

try {
    $stmt = $conexion->prepare('SELECT c.id_compra, c.numero_factura, p.nombre_empresa AS proveedor_nombre, c.subtotal, c.impuesto, c.total, c.fecha_compra, c.estado FROM compras c LEFT JOIN proveedores p ON p.id_proveedor = c.fk_proveedor ORDER BY c.id_compra DESC');
    $stmt->execute();
    $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $compras = [];
}

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="compras_export.csv"');
echo "ID,Factura,Proveedor,Subtotal,Impuesto,Total,Fecha,Estado\n";
foreach ($compras as $c) {
    $proveedor = str_replace(',', ' ', $c['proveedor_nombre'] ?? '');
    $line = [
        $c['id_compra'],
        $c['numero_factura'],
        $proveedor,
        number_format((float)$c['subtotal'], 2, '.', ''),
        number_format((float)$c['impuesto'], 2, '.', ''),
        number_format((float)$c['total'], 2, '.', ''),
        $c['fecha_compra'],
        $c['estado']
    ];
    echo implode(',', $line) . "\n";
}
exit;
