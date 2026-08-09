<?php
session_start();
$current = basename($_SERVER['SCRIPT_NAME']);
function active($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>
<nav class="sidebar d-flex flex-column p-3">
  <a href="inicio.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">MiApp</span>
  </a>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="inicio.php" class="nav-link <?php echo active('inicio.php'); ?>">Inicio</a></li>
    <li><a href="clientes.php" class="nav-link <?php echo active('clientes.php'); ?>">Clientes</a></li>
    <li><a href="productos.php" class="nav-link <?php echo active('productos.php'); ?>">Productos</a></li>
    <li><a href="ventas.php" class="nav-link <?php echo active('ventas.php'); ?>">Ventas</a></li>
    <li><a href="compras.php" class="nav-link <?php echo active('compras.php'); ?>">Compras</a></li>
    <li><a href="proveedores.php" class="nav-link <?php echo active('proveedores.php'); ?>">Proveedores</a></li>
    <li><a href="inventario.php" class="nav-link <?php echo active('inventario.php'); ?>">Inventario</a></li>
    <li><a href="reportes.php" class="nav-link <?php echo active('reportes.php'); ?>">Reportes</a></li>
    <li><a href="usuarios.php" class="nav-link <?php echo active('usuarios.php'); ?>">Usuarios</a></li>
    <li><a href="departamentos.php" class="nav-link <?php echo active('departamentos.php'); ?>">Departamentos</a></li>
    <li><a href="devoluciones.php" class="nav-link <?php echo active('devoluciones.php'); ?>">Devoluciones</a></li>
    <li><a href="auditoria.php" class="nav-link <?php echo active('auditoria.php'); ?>">Auditoria</a></li>
    <li><a href="caja.php" class="nav-link <?php echo active('caja.php'); ?>">Caja</a></li>
    <li><a href="punto_venta.php" class="nav-link <?php echo active('punto_venta.php'); ?>">Punto de Venta</a></li>
    <li><a href="facturacion.php" class="nav-link <?php echo active('facturacion.php'); ?>">Facturacion</a></li>
  </ul>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://via.placeholder.com/32" alt="avatar" class="rounded-circle me-2">
      <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></strong>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
      <li><a class="dropdown-item" href="javascript:void(0)" data-action="coming-soon">Perfil</a></li>
      <li><a class="dropdown-item" href="logout.php">Cerrar sesion</a></li>
    </ul>
  </div>
</nav>
