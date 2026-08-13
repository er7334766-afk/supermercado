<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('devoluciones');

try {
    $stmt = $conexion->prepare(
        "SELECT d.id_devolucion, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, d.motivo, d.fecha_devolucion, d.estado FROM devoluciones d LEFT JOIN ventas v ON v.id_venta = d.fk_venta LEFT JOIN clientes c ON c.id_cliente = v.fk_cliente ORDER BY d.id_devolucion DESC"
    );
    $stmt->execute();
    $devoluciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $devoluciones = [];
}

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="devoluciones_export.csv"');
echo "ID,Cliente,Motivo,Fecha,Estado\n";
foreach ($devoluciones as $d) {
    $cliente = trim(($d['cliente_nombre'] ?? '') . ' ' . ($d['cliente_apellido'] ?? ''));
    $line = [
        $d['id_devolucion'],
        str_replace(',', ' ', $cliente),
        str_replace(',', ' ', $d['motivo']),
        $d['fecha_devolucion'],
        $d['estado']
    ];
    echo implode(',', $line) . "\n";
}
exit;
