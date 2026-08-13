<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';
require_once 'config/acciones.php';

requerirAccesoAccion('departamentos', 'eliminar');

$id_departamento = (int)($_GET['id'] ?? 0);

if ($id_departamento <= 0) {
    header('Location: departamentos.php');
    exit;
}

try {

    $stmt = $conexion->prepare("
        DELETE FROM departamentos
        WHERE id_departamento = ?
    ");

    $stmt->execute([$id_departamento]);

    header('Location: departamentos.php');
    exit;

} catch (PDOException $e) {

    /*
     * Si tiene productos relacionados, MySQL puede impedir
     * eliminarlo por la llave foránea.
     */
    header(
        'Location: departamentos.php?error=' .
        urlencode('No se puede eliminar el departamento porque puede tener productos relacionados.')
    );

    exit;
}