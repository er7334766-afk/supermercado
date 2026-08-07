<!doctype html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Editar Compra</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet">

        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Compra</h2>

            <a href="compras.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="actualizar_compra.php" method="POST">

                    <input
                        type="hidden"
                        name="id_compra"
                        value="1">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Proveedor</label>

                            <select
                                class="form-select"
                                name="fk_proveedor"
                                required>

                                <option value="1" selected>
                                    Proveedor 1
                                </option>

                                <option value="2">
                                    Proveedor 2
                                </option>

                                <option value="3">
                                    Proveedor 3
                                </option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Número de Factura</label>

                            <input
                                type="text"
                                class="form-control"
                                name="numero_factura"
                                value="COMP-000001"
                                required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Fecha de Compra</label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                name="fecha_compra"
                                value="2026-08-06T18:30">

                        </div>

                    </div>

                    <hr>

                    <h5>Productos de la Compra</h5>

                    <table class="table table-bordered align-middle">

                        <thead class="table-dark">

                            <tr>
                                <th>Producto</th>
                                <th>Precio Compra</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>
                                    <select class="form-select">
                                        <option selected>Leche Sula</option>
                                        <option>Arroz</option>
                                        <option>Azúcar</option>
                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        value="28.50">
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        class="form-control"
                                        value="10">
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        value="285.00"
                                        readonly>
                                </td>

                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm">

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

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Subtotal</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="subtotal"
                                value="285.00"
                                readonly>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Impuesto</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="impuesto"
                                value="42.75"
                                readonly>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Total</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="total"
                                value="327.75"
                                readonly>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Estado</label>

                        <select
                            class="form-select"
                            name="estado">

                            <option value="Registrada" selected>
                                Registrada
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

                            Actualizar Compra

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