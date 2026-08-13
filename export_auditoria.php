<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('auditoria');

try {
    $stmt = $conexion->prepare("SELECT a.id_auditoria, u.usuario AS usuario, a.accion, a.descripcion AS detalle, a.fecha AS fecha_registro FROM auditoria a LEFT JOIN usuarios u ON u.id_usuario = a.fk_usuario ORDER BY a.id_auditoria DESC");
    $stmt->execute();
    $auditorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $auditorias = [];
}

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="auditoria_export.csv"');
echo "ID,Usuario,Accion,Detalle,Fecha\n";
foreach ($auditorias as $a) {
    $line = [
        $a['id_auditoria'],
        str_replace(',', ' ', $a['usuario'] ?? 'Sistema'),
        str_replace(',', ' ', $a['accion']),
        str_replace(',', ' ', $a['detalle'] ?? ''),
        $a['fecha_registro']
    ];
    echo implode(',', $line) . "\n";
}
exit;
