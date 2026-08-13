<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT id_cliente, nombre, apellido FROM clientes WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
    $error = 'No se pudieron cargar los clientes: ' . $e->getMessage();
}

try {
    $stmt = $conexion->prepare("SELECT id_producto, codigo_barras, nombre, precio_venta FROM productos WHERE estado = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
    $error = 'No se pudieron cargar los productos: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nueva Venta</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>
    <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center header-title">
            <h2>Nueva Venta</h2>
            <a href="ventas.php" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form action="guardar_venta.php" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" name="fk_cliente">
                                <option value="">Consumidor Final</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo intval($cliente['id_cliente']); ?>"><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Factura</label>
                            <input type="text" class="form-control" name="numero_factura" value="FAC-<?php echo date('YmdHis'); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="datetime-local" class="form-control" name="fecha_venta" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>
                    <hr>
                    <h5>Productos</h5>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select" name="producto[]">
                                        <option value="">Seleccione producto...</option>
                                        <?php foreach ($productos as $producto) : ?>
                                            <option value="<?php echo intval($producto['id_producto']); ?>" data-precio="<?php echo htmlspecialchars($producto['precio_venta']); ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control" name="precio[]" step="0.01" value="0.00" readonly></td>
                                <td><input type="number" class="form-control" name="cantidad[]" value="1" min="1"></td>
                                <td><input type="number" class="form-control" name="subtotal[]" step="0.01" value="0.00" readonly></td>
                                <td class="align-middle">
                                    <button type="button" id="add_to_cart" class="btn btn-sm btn-primary">Agregar al carrito</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <h5>Carrito</h5>
                        <div id="cart_container">
                            <div class="text-muted">Cargando carrito...</div>
                        </div>
                        <div class="mt-2">
                            <button type="button" id="clear_cart" class="btn btn-sm btn-danger">Vaciar carrito</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago">
                                <option>Efectivo</option>
                                <option>Tarjeta</option>
                                <option>Transferencia</option>
                                <option>Pago móvil</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto Recibido</label>
                            <input type="number" class="form-control" step="0.01" name="monto_recibido">
                        </div>
                    </div>
                    <hr>
                    <div class="row text-end">
                        <div class="col-md-12">
                            <div class="mb-2"><strong>Subtotal:</strong> <span id="display_subtotal">L. 0.00</span></div>
                            <div class="mb-2 row g-2">
                                <div class="col-md-4 text-end"><label class="form-label">Descuento</label></div>
                                <div class="col-md-2"><input type="number" class="form-control" name="descuento" id="descuento" step="0.01" value="0.00"></div>
                            </div>
                            <div class="mb-2"><strong>Impuesto:</strong> <span id="display_impuesto">L. 0.00</span></div>
                            <h4 class="text-success">Total: <span id="display_total">L. 0.00</span></h4>
                            <div class="mb-2"><strong>Cambio:</strong> <span id="display_cambio">L. 0.00</span></div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button class="btn btn-success">Guardar Venta</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var cartItems = [];

        function updateRow(row) {
            var select = row.querySelector('select[name="producto[]"]');
            var priceInput = row.querySelector('input[name="precio[]"]');
            var qtyInput = row.querySelector('input[name="cantidad[]"]');
            var subtotalInput = row.querySelector('input[name="subtotal[]"]');
            var price = 0;
            if (select && select.selectedOptions && select.selectedOptions[0]) {
                price = parseFloat(select.selectedOptions[0].dataset.precio || 0);
            }
            priceInput.value = price.toFixed(2);
            var qty = parseFloat(qtyInput.value) || 0;
            subtotalInput.value = (price * qty).toFixed(2);
        }

        function updateTotals() {
            var subtotal = 0;
            if (cartItems && cartItems.length > 0) {
                cartItems.forEach(function (it) { subtotal += (parseFloat(it.precio_unitario) || 0) * (parseInt(it.cantidad) || 0); });
            } else {
                document.querySelectorAll('input[name="subtotal[]"]').forEach(function (el) {
                    subtotal += parseFloat(el.value) || 0;
                });
            }
            var descuento = parseFloat(document.getElementById('descuento').value) || 0;
            var impuesto = +(subtotal * 0.15).toFixed(2);
            var total = +(subtotal - descuento + impuesto).toFixed(2);
            document.getElementById('display_subtotal').textContent = 'L. ' + subtotal.toFixed(2);
            document.getElementById('display_impuesto').textContent = 'L. ' + impuesto.toFixed(2);
            document.getElementById('display_total').textContent = 'L. ' + total.toFixed(2);

            var montoRecibidoEl = document.querySelector('input[name="monto_recibido"]');
            var cambio = 0;
            if (montoRecibidoEl) {
                var monto = parseFloat(montoRecibidoEl.value) || 0;
                cambio = +(monto - total).toFixed(2);
            }
            document.getElementById('display_cambio').textContent = 'L. ' + cambio.toFixed(2);
        }

        function fetchJSON(url, data) {
            return fetch(url, data).then(function (r) { return r.json(); });
        }

        function loadCart() {
            fetchJSON('carrito.php?action=list', { method: 'GET' }).then(function (res) {
                if (res.success) {
                    cartItems = res.items || [];
                    renderCart();
                    updateTotals();
                } else {
                    document.getElementById('cart_container').innerHTML = '<div class="text-danger">No se pudo cargar el carrito</div>';
                }
            }).catch(function () { document.getElementById('cart_container').innerHTML = '<div class="text-danger">Error cargando carrito</div>'; });
        }

        function renderCart() {
            var container = document.getElementById('cart_container');
            if (!cartItems || cartItems.length === 0) {
                container.innerHTML = '<div class="text-muted">El carrito está vacío.</div>';
                return;
            }
            var html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead><tbody>';
            cartItems.forEach(function (it) {
                var lineSub = (parseFloat(it.precio_unitario) || 0) * (parseInt(it.cantidad) || 0);
                html += '<tr data-producto="' + escapeHtml(it.fk_producto || it.fk_producto || '') + '">';
                html += '<td>' + escapeHtml(it.nombre || '') + '</td>';
                html += '<td>L. ' + (parseFloat(it.precio_unitario) || 0).toFixed(2) + '</td>';
                html += '<td><input type="number" min="0" class="form-control form-control-sm cart-qty" value="' + (parseInt(it.cantidad) || 0) + '" style="width:80px"></td>';
                html += '<td>L. ' + lineSub.toFixed(2) + '</td>';
                html += '<td><button class="btn btn-sm btn-danger btn-remove">Eliminar</button></td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function escapeHtml(text) {
            return String(text).replace(/[&"'<>]/g, function (m) { return ({'&':'&amp;','"':'&quot;',"'":'&#39;','<':'&lt;','>':'&gt;'})[m]; });
        }

        // events
        document.querySelectorAll('select[name="producto[]"]').forEach(function (sel) {
            sel.addEventListener('change', function (e) {
                var row = e.target.closest('tr');
                updateRow(row);
                updateTotals();
            });
        });

        document.querySelectorAll('input[name="cantidad[]"]').forEach(function (inp) {
            inp.addEventListener('input', function (e) {
                var row = e.target.closest('tr');
                updateRow(row);
                updateTotals();
            });
        });

        document.getElementById('descuento').addEventListener('input', function () { updateTotals(); });
        var montoRec = document.querySelector('input[name="monto_recibido"]');
        if (montoRec) montoRec.addEventListener('input', function () { updateTotals(); });

        // add to cart
        var addBtn = document.getElementById('add_to_cart');
        if (addBtn) addBtn.addEventListener('click', function () {
            var row = addBtn.closest('tr');
            var sel = row.querySelector('select[name="producto[]"]');
            var qty = row.querySelector('input[name="cantidad[]"]');
            var producto = sel.value || '';
            var cantidad = parseInt(qty.value) || 1;
            if (!producto) { alert('Seleccione un producto'); return; }
            fetchJSON('carrito.php?action=add', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'producto=' + encodeURIComponent(producto) + '&cantidad=' + encodeURIComponent(cantidad) })
                .then(function (res) { if (res.success) { loadCart(); alert('Producto agregado al carrito'); } else alert(res.message || 'Error'); })
                .catch(function () { alert('Error agregando al carrito'); });
        });

        // clear cart
        var clearBtn = document.getElementById('clear_cart');
        if (clearBtn) clearBtn.addEventListener('click', function () {
            if (!confirm('¿Vaciar el carrito?')) return;
            fetchJSON('carrito.php?action=clear', { method: 'POST' }).then(function (res) { if (res.success) { loadCart(); } else alert('Error'); });
        });

        // delegate cart update/remove
        document.getElementById('cart_container').addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove')) {
                var tr = e.target.closest('tr');
                var producto = tr.getAttribute('data-producto');
                fetchJSON('carrito.php?action=remove', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'producto=' + encodeURIComponent(producto) })
                    .then(function (res) { if (res.success) loadCart(); else alert('Error'); });
            }
        });

        document.getElementById('cart_container').addEventListener('input', function (e) {
            if (e.target.classList.contains('cart-qty')) {
                var tr = e.target.closest('tr');
                var producto = tr.getAttribute('data-producto');
                var cantidad = parseInt(e.target.value) || 0;
                fetchJSON('carrito.php?action=update', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'producto=' + encodeURIComponent(producto) + '&cantidad=' + encodeURIComponent(cantidad) })
                    .then(function (res) { if (res.success) loadCart(); else alert('Error'); });
            }
        });

        // inicializa
        document.querySelectorAll('tr').forEach(function (r) { if (r.querySelector('select[name="producto[]"]')) updateRow(r); });
        loadCart();
        updateTotals();
    });
    </script>
    </body>
</html>