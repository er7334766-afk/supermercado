<?php
/**
 * Funciones de auditoría
 */

/**
 * Registra una entrada en la tabla `auditoria`.
 *
 * @param PDO $conexion Conexión PDO
 * @param int|null $fk_usuario ID del usuario (puede ser null para acciones del sistema)
 * @param string $accion Acción breve
 * @param string|null $descripcion Detalle o descripción
 */
function registrarAuditoria($conexion, $fk_usuario, $accion, $descripcion = null, $modulo = 'sistema', $tabla_afectada = null, $id_registro = null, $ip = null) {
    try {
        // Detectar IP si no se provee
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }

        // Preparar INSERT con columnas compatibles con el esquema del proyecto
        $stmt = $conexion->prepare(
            'INSERT INTO auditoria (fk_usuario, modulo, accion, tabla_afectada, id_registro, descripcion, ip, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $fk_usuario !== null ? $fk_usuario : null,
            $modulo,
            $accion,
            $tabla_afectada,
            $id_registro,
            $descripcion,
            $ip
        ]);
    } catch (Throwable $e) {
        error_log('Auditoría fallida: ' . $e->getMessage());
    }
}

?>
