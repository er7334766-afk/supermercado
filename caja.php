<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAcceso('caja');

// try {
//     $stmt = $conexion->query(
//         "SELECT c.id_caja, c.nombre, c.ubicacion, c.estado, a.monto_inicial, a.monto_contado "
//         . "FROM cajas c "
//         . "LEFT JOIN aperturas_caja a ON a.fk_caja = c.id_caja "
//         . "ORDER BY a.id_apertura DESC LIMIT 1"
//     );
//     $caja = $stmt->fetch();
//     if (!$caja) {
//         $caja = [
//             'id_caja' => 0,
//             'nombre' => 'Sin caja registrada',
//             'ubicacion' => '-',
//             'estado' => 'Cerrada',
//             'monto_inicial' => 0,
//             'monto_contado' => 0,
//         ];
//     }
// } catch (PDOException $e) {
//     $caja = [
//         'id_caja' => 0,
//         'nombre' => 'Sin caja registrada',
//         'ubicacion' => '-',
//         'estado' => 'Cerrada',
//         'monto_inicial' => 0,
//         'monto_contado' => 0,
//     ];
//     $error = 'No se pudo cargar la caja: ' . $e->getMessage();
// }

try {
    $stmt = $conexion->query("
        SELECT 
            c.id_caja,
            c.nombre,
            c.ubicacion,
            c.estado,
            a.id_apertura,
            a.monto_inicial,
            a.monto_contado,
            a.estado AS estado_apertura
        FROM cajas c
        LEFT JOIN aperturas_caja a 
            ON a.id_apertura = (
                SELECT MAX(a2.id_apertura)
                FROM aperturas_caja a2
                WHERE a2.fk_caja = c.id_caja
            )
        ORDER BY c.id_caja ASC
    ");

    $cajas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $cajas = [];

    $error = 'No se pudieron cargar las cajas: ' . $e->getMessage();
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caja - Aplicacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/supermercado-main/static/css/styles.css?v=3">
  </head>
  <body>
    <?php include "menu/_layout_sidebar.php"; ?>
    <main class="content">
      <div class="header-title d-flex justify-content-between align-items-center">
        <h2>Caja</h2>
        <div>
          <a class="btn btn-primary me-2" href="nueva_apertura.php">Abrir caja</a>
          <a class="btn btn-outline-primary" href="nueva_caja.php">Crear caja</a>
        </div>
      </div>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <div class="card shadow-sm">
        
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/scripts.js"></script>
</body>
</html>
<?php foreach ($cajas as $caja) : ?>

    <?php
        $estaAbierta = strtolower($caja['estado']) === 'abierto'
                    || strtolower($caja['estado']) === 'abierta';

        $borde = $estaAbierta ? 'border-success' : 'border-secondary';
        $badge = $estaAbierta ? 'bg-success' : 'bg-secondary';
    ?>

    <div class="card shadow-sm mb-3 border-2 mx-auto w-100<?php echo $borde; ?>" style="max-width: 1100px;">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <?php echo htmlspecialchars($caja['nombre']); ?>
                </h5>

                <span class="badge <?php echo $badge; ?>">
                    <?php echo htmlspecialchars($caja['estado']); ?>
                </span>
            </div>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <p class="mb-1 text-muted">Ubicación</p>
                    <strong>
                        <?php echo htmlspecialchars($caja['ubicacion']); ?>
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <p class="mb-1 text-muted">Estado</p>
                    <strong>
                        <?php echo $estaAbierta ? 'Caja disponible' : 'Caja cerrada'; ?>
                    </strong>
                </div>

                <div class="col-md-4 mb-3">
                    <p class="mb-1 text-muted">Saldo Actual</p>

                    <h5 class="<?php echo $estaAbierta ? 'text-success' : 'text-muted'; ?>">
                        L. <?php echo number_format(
                            (float)($caja['monto_contado'] ?? 0),
                            2,
                            '.',
                            ','
                        ); ?>
                    </h5>
                </div>

            </div>

        </div>
    </div>

<?php endforeach; ?>