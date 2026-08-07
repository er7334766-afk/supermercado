<!doctype html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Editar Venta</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet">

        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Venta</h2>

            <a href="ventas.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="actualizar_venta.php" method="POST">

                    <!-- ID de la venta -->
                    <input type="hidden" name="id_venta" value="1001">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Número de Factura</label>

                            <input
                                type="text"
                                class="form-control"
                                name="numero_factura"
                                value="FAC-000001"
                                readonly>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Cliente</label>

                            <select class="form-select" name="fk_cliente">

                                <option value="1" selected>Consumidor Final</option>
                                <option value="2">Juan Pérez</option>
                                <option value="3">María López</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Fecha</label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                name="fecha_venta"
                                value="2026-08-06T18:30">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3 mb-3">

                            <label class="form-label">Subtotal</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="subtotal"
                                value="100.00"
                                readonly>

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">Descuento</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="descuento"
                                value="0.00">

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">Impuesto</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="impuesto"
                                value="15.00"
                                readonly>

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">Total</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="total"
                                value="115.00"
                                readonly>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Método de Pago</label>

                            <select class="form-select" name="metodo_pago">

                                <option selected>Efectivo</option>
                                <option>Tarjeta</option>
                                <option>Transferencia</option>
                                <option>Pago móvil</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Monto Recibido</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="monto_recibido"
                                value="120.00">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Cambio</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="cambio"
                                value="5.00"
                                readonly>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Estado</label>

                        <select class="form-select" name="estado">

                            <option value="Completada" selected>
                                Completada
                            </option>

                            <option value="Pendiente">
                                Pendiente
                            </option>

                            <option value="Anulada">
                                Anulada
                            </option>

                        </select>

                    </div>

                    <div class="text-end">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Actualizar Venta

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="static/js/scripts.js"></script>

    </body>
</html>