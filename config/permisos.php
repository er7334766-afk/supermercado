<?php
/**
 * Sistema de Permisos y Control de Acceso
 * Define los permisos de cada rol para los módulos del sistema
 */

// IDs de los roles
const ROLE_ADMIN = 1;
const ROLE_CAJERO = 2;
const ROLE_BODEGUERO = 3;

// Definición de permisos por rol
$PERMISOS_POR_ROL = [
    ROLE_ADMIN => [
        'inicio' => true,
        'productos' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'clientes' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'proveedores' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'compras' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'ventas' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'caja' => true,
        'usuarios' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
        'reportes' => true,
        'departamentos' => true,
        'devoluciones' => true,
        'auditoria' => true,
        'inventario' => true,
        'punto_venta' => true,
        'facturacion' => true,
    ],
    ROLE_CAJERO => [
        'inicio' => true,
        'productos' => ['ver' => true, 'crear' => false, 'editar' => false, 'eliminar' => false],
        'clientes' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
        'ventas' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
        'caja' => true,
        // No tiene acceso a otros módulos
    ],
    ROLE_BODEGUERO => [
        'inicio' => true,
        'productos' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
        'proveedores' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
        'compras' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
        'inventario' => true,
        // No tiene acceso a otros módulos
    ],
];

/**
 * Verifica si el usuario actual tiene acceso a un módulo específico
 * 
 * @param string $modulo Nombre del módulo (ej: 'productos', 'usuarios')
 * @param string $accion Acción a verificar: 'ver', 'crear', 'editar', 'eliminar' (opcional)
 * @return bool true si tiene acceso, false si no
 */
function verificarAcceso($modulo, $accion = null) {
    global $PERMISOS_POR_ROL;
    
    if (!isset($_SESSION['usuario_rol'])) {
        return false;
    }
    
    $rolId = (int)$_SESSION['usuario_rol'];
    
    // Si el rol no existe en la configuración, denegar acceso
    if (!isset($PERMISOS_POR_ROL[$rolId])) {
        return false;
    }
    
    $permisos = $PERMISOS_POR_ROL[$rolId];
    
    // Si el módulo no existe para este rol, denegar acceso
    if (!isset($permisos[$modulo])) {
        return false;
    }
    
    $permisoModulo = $permisos[$modulo];
    
    // Si es true, tiene acceso total
    if ($permisoModulo === true) {
        return true;
    }
    
    // Si no se especifica acción y es un array, verificar que sea array (acceso condicional)
    if ($accion === null) {
        return is_array($permisoModulo) || $permisoModulo === true;
    }
    
    // Si es un array, verificar la acción específica
    if (is_array($permisoModulo)) {
        return isset($permisoModulo[$accion]) && $permisoModulo[$accion] === true;
    }
    
    return false;
}

/**
 * Redirige si el usuario no tiene acceso, con mensaje de error
 * 
 * @param string $modulo Nombre del módulo
 * @param string $accion Acción a verificar (opcional)
 */
function requerirAcceso($modulo, $accion = null) {
    if (!verificarAcceso($modulo, $accion)) {
        $_SESSION['acceso_denegado'] = "No tienes permiso para acceder a este módulo.";
        header('Location: inicio.php');
        exit;
    }
}

/**
 * Obtiene los permisos del rol actual
 * 
 * @return array Permisos del rol actual o array vacío
 */
function obtenerPermisosRolActual() {
    global $PERMISOS_POR_ROL;
    
    if (!isset($_SESSION['usuario_rol'])) {
        return [];
    }
    
    $rolId = (int)$_SESSION['usuario_rol'];
    return $PERMISOS_POR_ROL[$rolId] ?? [];
}

/**
 * Obtiene el nombre del rol basado en su ID
 * 
 * @param int $rolId ID del rol
 * @return string Nombre del rol
 */
function obtenerNombreRol($rolId) {
    $nombres = [
        ROLE_ADMIN => 'Administrador',
        ROLE_CAJERO => 'Cajero',
        ROLE_BODEGUERO => 'Bodeguero',
    ];
    
    return $nombres[$rolId] ?? 'Desconocido';
}

?>
