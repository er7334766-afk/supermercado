<?php
/**
 * Manejador de mensajes de acceso denegado
 * Muestra un mensaje amigable cuando un usuario no tiene permiso
 */

// Si hay un mensaje de acceso denegado en la sesión, mostrarlo y limpiarlo
function mostrarMensajeAccesoDenegado() {
    if (isset($_SESSION['acceso_denegado'])) {
        $mensaje = htmlspecialchars($_SESSION['acceso_denegado']);
        unset($_SESSION['acceso_denegado']);
        return $mensaje;
    }
    return null;
}

?>
