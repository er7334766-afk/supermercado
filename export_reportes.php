<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('reportes');

try {
    $ventasResumen = $conexion->query("SELECT COUNT(*) AS total_ventas, COALESCE(SUM(total), 0) AS total_monto FROM ventas WHERE estado = 'Completada'")->fetch(PDO::FETCH_ASSOC);
    $inventarioProductos = $conexion->query("SELECT id_producto, nombre, existencia, existencia_minima FROM productos WHERE estado = 1 AND existencia < existencia_minima ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $ventasRecientes = $conexion->query(
        "SELECT v.id_venta, v.numero_factura, v.total, v.fecha_venta, (SELECT COUNT(*) FROM devoluciones d WHERE d.fk_venta = v.id_venta) AS devoluciones_count "
        . "FROM ventas v ORDER BY v.id_venta DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: reportes.php?error=' . urlencode($e->getMessage()));
    exit;
}

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="reportes_export.csv"');
echo "\xEF\xBB\xBF"; // BOM for Excel UTF-8

// Sección: Resumen de Ventas
echo "Sección: Resumen de Ventas\n";
echo "Total ventas,Total monto\n";
echo intval($ventasResumen['total_ventas']) . ',' . number_format((float)$ventasResumen['total_monto'], 2, '.', '') . "\n\n";

// Sección: Inventario - Productos con stock bajo
echo "Sección: Inventario - Productos con stock bajo\n";
echo "ID,Nombre,Existencia,Minimo\n";
if (!empty($inventarioProductos)) {
    foreach ($inventarioProductos as $p) {
        $line = [
            $p['id_producto'],
            str_replace(',', ' ', $p['nombre']),
            $p['existencia'],
            $p['existencia_minima']
        ];
        echo implode(',', $line) . "\n";
    }
} else {
    echo "No hay productos con stock bajo\n";
}
echo "\n";

// Sección: Últimas ventas
echo "Sección: Últimas ventas\n";
echo "Factura,Total,Fecha,Devolucion\n";
if (!empty($ventasRecientes)) {
    foreach ($ventasRecientes as $v) {
        $tiene = (!empty($v['devoluciones_count']) && (int)$v['devoluciones_count'] > 0) ? 'Sí' : 'No';
        $line = [
            $v['numero_factura'],
            number_format((float)$v['total'], 2, '.', ''),
            $v['fecha_venta'],
            $tiene
        ];
        echo implode(',', $line) . "\n";
    }
} else {
    echo "No hay ventas registradas\n";
}

exit;
