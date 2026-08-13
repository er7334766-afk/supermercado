<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('caja');
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Caja - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Crear Caja</h2>
      </div>

      <div class="card"><div class="card-body">
        <form method="post" action="guardar_caja.php">
          <div class="mb-3">
            <label class="form-label">Nombre de la caja</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
              <option value="Cerrada">Cerrada</option>
              <option value="Abierta">Abierta</option>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Crear caja</button>
            <a href="caja.php" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
  </body>
  </html>
