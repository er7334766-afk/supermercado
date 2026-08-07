<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Producto</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="static/css/styles.css">
    </head>

    <body>

    <?php include "menu/_layout_sidebar.php"; ?>

    <main class="content">

        <div class="d-flex justify-content-between align-items-center header-title">

            <h2>Editar Producto</h2>

            <a href="productos.php" class="btn btn-secondary">
                Volver
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id_producto" value="1">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento</label>

                            <select class="form-select" name="fk_departamento">

                                <option value="1" selected>Lácteos</option>
                                <option value="2">Bebidas</option>
                                <option value="3">Abarrotes</option>
                                <option value="4">Carnes</option>

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Código de Barras</label>

                            <input
                                type="text"
                                class="form-control"
                                name="codigo_barras"
                                value="750123456789">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Nombre del Producto</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nombre"
                            value="Leche Sula Entera">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Descripción</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="descripcion">Leche entera de 1 litro.</textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Precio Compra</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="precio_compra"
                                value="28.50">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Precio Venta</label>

                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                name="precio_venta"
                                value="35.00">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Existencia</label>

                            <input
                                type="number"
                                class="form-control"
                                name="existencia"
                                value="80">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Existencia Mínima</label>

                            <input
                                type="number"
                                class="form-control"
                                name="existencia_minima"
                                value="10">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Unidad de Medida</label>

                            <select class="form-select" name="unidad_medida">

                                <option selected>Unidad</option>
                                <option>Libra</option>
                                <option>Kilogramo</option>
                                <option>Litro</option>
                                <option>Caja</option>
                                <option>Paquete</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">Fecha de Vencimiento</label>

                            <input
                                type="date"
                                class="form-control"
                                name="fecha_vencimiento"
                                value="2027-12-31">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Imagen</label>

                            <input
                                type="file"
                                class="form-control"
                                name="imagen">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">Estado</label>

                            <select class="form-select" name="estado">

                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>

                            </select>

                        </div>

                    </div>

                    <div class="text-end">

                        <button
                            type="submit"
                            class="btn btn-success">

                            Actualizar Producto

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