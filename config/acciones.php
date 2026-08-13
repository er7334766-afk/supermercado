<?php
/**
 * Mapeo de archivos de acción a permisos
 * Facilita el control de acceso en operaciones CRUD
 */

/**
 * Verifica si el usuario tiene permiso para ejecutar una acción específica en un módulo
 * Si no tiene permiso, redirige al inicio y detiene la ejecución
 * 
 * @param string $modulo El módulo (ej: 'productos', 'clientes')
 * @param string $accion La acción (ej: 'crear', 'editar', 'eliminar')
 */
function requerirAccesoAccion($modulo, $accion) {
    if (!verificarAcceso($modulo, $accion)) {
        $_SESSION['acceso_denegado'] = "No tienes permiso para $accion en $modulo.";
        header('Location: inicio.php');
        exit;
    }
}

?>
