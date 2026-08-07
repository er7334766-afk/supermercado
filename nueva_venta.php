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

            <a href="ventas.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="guardar_venta.php" method="POST">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" name="fk_cliente">
                                <option>Consumidor Final</option>
                                <option>Juan Pérez</option>
                                <option>María López</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Factura</label>
                            <input
                                type="text"
                                class="form-control"
                                name="numero_factura"
                                value="FAC-000001"
                                readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input
                                type="datetime-local"
                                class="form-control"
                                name="fecha_venta">
                        </div>

                    </div>

                    <hr>

                    <h5>Productos</h5>

                    <table class="table table-bordered">

                        <thead class="table-dark">

                            <tr>

                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>
                                    <input type="text" class="form-control">
                                </td>

                                <td>
                                    <input type="text" class="form-control">
                                </td>

                                <td>
                                    <input type="number" class="form-control">
                                </td>

                                <td>
                                    <input type="number" class="form-control" value="1">
                                </td>

                                <td>
                                    <input type="number" class="form-control" readonly>
                                </td>

                                <td>

                                    <button type="button" class="btn btn-danger btn-sm">
                                        X
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                    <button
                        type="button"
                        class="btn btn-outline-primary mb-4">

                        + Agregar Producto

                    </button>

                    <div class="row">

                        <div class="col-md-6">

                            <label class="form-label">Método de Pago</label>

                            <select
                                class="form-select"
                                name="metodo_pago">

                                <option>Efectivo</option>
                                <option>Tarjeta</option>
                                <option>Transferencia</option>
                                <option>Pago móvil</option>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Monto Recibido</label>

                            <input
                                type="number"
                                class="form-control"
                                step="0.01"
                                name="monto_recibido">

                        </div>

                    </div>

                    <hr>

                    <div class="row text-end">

                        <div class="col-md-12">

                            <h5>Subtotal: L. 0.00</h5>

                            <h5>Descuento: L. 0.00</h5>

                            <h5>Impuesto: L. 0.00</h5>

                            <h4 class="text-success">

                                Total: L. 0.00

                            </h4>

                            <h5>Cambio: L. 0.00</h5>

                        </div>

                    </div>

                    <div class="text-end mt-4">

                        <button
                            class="btn btn-success">

                            Guardar Venta

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
    </body>
</html>