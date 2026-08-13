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
    $stmt = $conexion->prepare('SELECT id_venta, numero_factura, fecha_venta FROM ventas WHERE estado = "Completada" ORDER BY id_venta DESC');
    $stmt->execute();
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ventas = [];
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Devolución - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/styles.css">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title"><h2>Nueva Devolución</h2></div>
      <div class="card"><div class="card-body">
        <form method="post" action="guardar_devolucion.php">
          <div class="mb-3">
            <label class="form-label">Venta</label>
            <select class="form-select" name="fk_venta" required>
              <option value="">-- Seleccione una venta --</option>
              <?php foreach ($ventas as $v): ?>
                <option value="<?php echo intval($v['id_venta']); ?>"><?php echo htmlspecialchars($v['numero_factura'] . ' - ' . $v['fecha_venta']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Motivo</label>
            <textarea name="motivo" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3" id="detalles_devolucion_container" style="display:none">
            <label class="form-label">Detalle de devolución</label>
            <div id="detalles_devolucion"></div>
            <small class="text-muted">Ingrese las cantidades a devolver por producto (0 para no devolver).</small>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Registrar devolución</button>
            <a href="devoluciones.php" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      var sel = document.querySelector('select[name="fk_venta"]');
      var container = document.getElementById('detalles_devolucion');
      var wrapper = document.getElementById('detalles_devolucion_container');

      sel.addEventListener('change', function(){
        var id = sel.value || 0;
        container.innerHTML = '';
        wrapper.style.display = 'none';
        if (!id) return;

        fetch('venta_detalle.php?id=' + encodeURIComponent(id))
          .then(function(r){ return r.json(); })
          .then(function(data){
            if (!data.success) { alert(data.message || 'Error al cargar detalles'); return; }
            var html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Precio</th><th>Vendidos</th><th>Cantidad a devolver</th><th>Subtotal</th></tr></thead><tbody>';
            data.items.forEach(function(it, idx){
              var precio = parseFloat(it.precio_unitario) || 0;
              var vendidos = parseInt(it.cantidad) || 0;
              html += '<tr>';
              html += '<td>' + (it.nombre || 'Producto') + '</td>';
              html += '<td>L. ' + precio.toFixed(2) + '</td>';
              html += '<td>' + vendidos + '</td>';
              html += '<td><input type="number" name="producto[]" value="' + it.fk_producto + '" hidden><input type="number" min="0" max="' + vendidos + '" class="form-control form-control-sm qty_retornar" name="cantidad[]" value="0"></td>';
              html += '<td class="subtotal_retornar">L. 0.00</td>';
              html += '</tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;
            wrapper.style.display = '';

            // actualizar subtotales cuando cambie cantidad
            container.querySelectorAll('.qty_retornar').forEach(function(inp, i){
              inp.addEventListener('input', function(){
                var row = inp.closest('tr');
                var precio = parseFloat(data.items[i].precio_unitario) || 0;
                var qty = parseInt(inp.value) || 0;
                row.querySelector('.subtotal_retornar').textContent = 'L. ' + (precio * qty).toFixed(2);
              });
            });
          }).catch(function(){ alert('Error en la petición'); });
      });
    });
    </script>
  </body>
  </html>
