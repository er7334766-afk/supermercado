<?php
/**
 * Verificador de Integridad - Sistema de Permisos
 * 
 * Este archivo verifica que todos los componentes del sistema de permisos
 * estén correctamente instalados y funcionando.
 * 
 * Uso: Abre en el navegador: http://localhost/supermercado-main/verificador.php
 * (Requiere tener sesión iniciada)
 */

session_start();
require_once 'config/conexion.php';

if (empty($_SESSION['usuario_id'])) {
    die('<div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">
        ⚠️ <strong>Debes iniciar sesión primero</strong><br>
        <a href="index.php">Ir a inicio de sesión</a>
    </div>');
}

// Verificar que el sistema de permisos esté instalado
$checks = [];

// Check 1: Archivo de permisos
$checks['config/permisos.php'] = file_exists('config/permisos.php');
if ($checks['config/permisos.php']) {
    require_once 'config/permisos.php';
    $checks['Constantes de roles'] = defined('ROLE_ADMIN') && defined('ROLE_CAJERO') && defined('ROLE_BODEGUERO');
    $checks['Función verificarAcceso()'] = function_exists('verificarAcceso');
    $checks['Función requerirAcceso()'] = function_exists('requerirAcceso');
}

// Check 2: Archivo de acciones
$checks['config/acciones.php'] = file_exists('config/acciones.php');
if ($checks['config/acciones.php']) {
    require_once 'config/acciones.php';
    $checks['Función requerirAccesoAccion()'] = function_exists('requerirAccesoAccion');
}

// Check 3: Archivo de mensajes
$checks['config/mensajes.php'] = file_exists('config/mensajes.php');
if ($checks['config/mensajes.php']) {
    require_once 'config/mensajes.php';
    $checks['Función mostrarMensajeAccesoDenegado()'] = function_exists('mostrarMensajeAccesoDenegado');
}

// Check 4: Sesión de usuario
$checks['$_SESSION[usuario_id]'] = isset($_SESSION['usuario_id']);
$checks['$_SESSION[usuario_rol]'] = isset($_SESSION['usuario_rol']);

// Check 5: Menú dinámico
$checks['menu/_layout_sidebar.php'] = file_exists('menu/_layout_sidebar.php');
if ($checks['menu/_layout_sidebar.php']) {
    $content = file_get_contents('menu/_layout_sidebar.php');
    $checks['Menú tiene verificarAcceso()'] = strpos($content, 'verificarAcceso') !== false;
}

// Check 6: Archivos de documentación
$checks['DOCUMENTACION_PERMISOS.md'] = file_exists('DOCUMENTACION_PERMISOS.md');
$checks['GUIA_INICIO_RAPIDO.md'] = file_exists('GUIA_INICIO_RAPIDO.md');
$checks['RESUMEN_CAMBIOS.md'] = file_exists('RESUMEN_CAMBIOS.md');

// Obtener información del usuario actual
$usuario_rol = (int)($_SESSION['usuario_rol'] ?? 0);
$nombre_rol = 'Desconocido';
if ($usuario_rol === 1) $nombre_rol = 'Administrador';
elseif ($usuario_rol === 2) $nombre_rol = 'Cajero';
elseif ($usuario_rol === 3) $nombre_rol = 'Bodeguero';

// Obtener permisos del rol actual
$permisos_rol = [];
if (function_exists('obtenerPermisosRolActual')) {
    $permisos_rol = obtenerPermisosRolActual();
}

$total = count($checks);
$exitosos = count(array_filter($checks));
$porcentaje = round(($exitosos / $total) * 100, 1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador - Sistema de Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; }
        .check-item { padding: 10px 15px; margin: 5px 0; border-radius: 5px; font-weight: 500; }
        .check-success { background-color: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .check-error { background-color: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .progress { height: 30px; margin: 20px 0; }
        .progress-bar { font-size: 14px; line-height: 30px; }
        .info-box { background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .modulo-item { padding: 8px 12px; margin: 3px; background: #f0f0f0; border-radius: 3px; display: inline-block; }
        h1 { color: #333; margin-bottom: 30px; }
        h3 { color: #555; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #2196F3; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificador de Integridad - Sistema de Permisos</h1>

        <!-- Información del usuario -->
        <div class="info-box">
            <strong>Usuario actual:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Desconocido'); ?><br>
            <strong>Rol:</strong> <?php echo htmlspecialchars($nombre_rol); ?> (ID: <?php echo $usuario_rol; ?>)
        </div>

        <!-- Estado general -->
        <div class="progress">
            <div class="progress-bar bg-<?php echo $porcentaje >= 95 ? 'success' : ($porcentaje >= 80 ? 'warning' : 'danger'); ?>" 
                 role="progressbar" style="width: <?php echo $porcentaje; ?>%;" 
                 aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $exitosos; ?>/<?php echo $total; ?> verificaciones completadas (<?php echo $porcentaje; ?>%)
            </div>
        </div>

        <!-- Verificación de archivos y funciones -->
        <h3>📋 Verificación de Instalación</h3>
        
        <?php foreach ($checks as $nombre => $resultado): ?>
            <div class="check-item <?php echo $resultado ? 'check-success' : 'check-error'; ?>">
                <?php echo $resultado ? '✅' : '❌'; ?> 
                <?php echo htmlspecialchars($nombre); ?>
            </div>
        <?php endforeach; ?>

        <!-- Permisos del rol actual -->
        <?php if (!empty($permisos_rol)): ?>
        <h3>🔐 Permisos del rol actual</h3>
        <p>Módulos a los que tienes acceso:</p>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
            <?php 
            $modulos_accesibles = [];
            foreach ($permisos_rol as $modulo => $permiso) {
                if ($permiso === true || is_array($permiso)) {
                    $modulos_accesibles[] = $modulo;
                }
            }
            
            if (!empty($modulos_accesibles)) {
                foreach ($modulos_accesibles as $modulo) {
                    echo '<span class="modulo-item">';
                    echo $modulo;
                    if (is_array($permisos_rol[$modulo])) {
                        $acciones = [];
                        if ($permisos_rol[$modulo]['ver'] ?? false) $acciones[] = 'ver';
                        if ($permisos_rol[$modulo]['crear'] ?? false) $acciones[] = 'crear';
                        if ($permisos_rol[$modulo]['editar'] ?? false) $acciones[] = 'editar';
                        if ($permisos_rol[$modulo]['eliminar'] ?? false) $acciones[] = 'eliminar';
                        echo ' (' . implode(', ', $acciones) . ')';
                    }
                    echo '</span>';
                }
            } else {
                echo '<em>No tienes acceso a ningún módulo (esto es inusual)</em>';
            }
            ?>
        </div>
        <?php endif; ?>

        <!-- Acciones sugeridas -->
        <h3>💡 Acciones Sugeridas</h3>
        <?php if ($porcentaje >= 95): ?>
            <div class="alert alert-success">
                <strong>✅ Sistema completamente instalado</strong><br>
                El sistema de permisos está listo para usar. 
                <a href="GUIA_INICIO_RAPIDO.md" target="_blank">Lee la guía de inicio rápido</a>
                para empezar las pruebas.
            </div>
        <?php elseif ($porcentaje >= 80): ?>
            <div class="alert alert-warning">
                <strong>⚠️ Sistema parcialmente instalado</strong><br>
                Faltan algunos componentes. 
                <a href="RESUMEN_CAMBIOS.md" target="_blank">Revisa los cambios esperados</a>
                para completar la instalación.
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <strong>❌ Sistema no completamente instalado</strong><br>
                Hay componentes faltantes. 
                <a href="IMPLEMENTACION_COMPLETADA.md" target="_blank">Lee el documento de implementación</a>
                para obtener ayuda.
            </div>
        <?php endif; ?>

        <!-- Pruebas rápidas -->
        <h3>🧪 Pruebas Rápidas</h3>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
            <p><strong>Acceso actual:</strong> Tienes permisos para acceder a esta página de verificación.</p>
            
            <?php 
            // Simular verificación de acceso
            if (function_exists('verificarAcceso')) {
                $acceso_inicio = verificarAcceso('inicio');
                $acceso_usuarios = verificarAcceso('usuarios');
                echo '<p>';
                echo '✅ Acceso a "inicio": ' . ($acceso_inicio ? 'SÍ ✅' : 'NO ❌') . '<br>';
                echo '✅ Acceso a "usuarios": ' . ($acceso_usuarios ? 'SÍ (solo Admin)' : 'NO (no eres Admin)') . '<br>';
                echo '</p>';
            }
            ?>
        </div>

        <!-- Enlaces útiles -->
        <h3>📚 Documentación</h3>
        <div class="row">
            <div class="col-md-6">
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Para empezar:</strong><br>
                    <a href="GUIA_INICIO_RAPIDO.md" target="_blank">📖 Guía de Inicio Rápido</a>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Referencia completa:</strong><br>
                    <a href="DOCUMENTACION_PERMISOS.md" target="_blank">📖 Documentación Completa</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div style="background: #fff3e0; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Cambios realizados:</strong><br>
                    <a href="RESUMEN_CAMBIOS.md" target="_blank">📋 Resumen de Cambios</a>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background: #fce4ec; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                    <strong>Estructura de BD:</strong><br>
                    <a href="SQL_ROLES.sql" target="_blank">🗄️ Script SQL</a>
                </div>
            </div>
        </div>

        <!-- Volver -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="inicio.php" class="btn btn-primary">← Volver a Inicio</a>
        </div>
    </div>
</body>
</html>
