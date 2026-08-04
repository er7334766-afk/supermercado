<?php
$current = basename($_SERVER['SCRIPT_NAME']);
function active($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>
<nav class="sidebar d-flex flex-column p-3">
  <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">MiApp</span>
  </a>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="dashboard.php" class="nav-link <?php echo active('dashboard.php'); ?>">Dashboard</a></li>
    <li><a href="customers.php" class="nav-link <?php echo active('customers.php'); ?>">Clientes</a></li>
    <li><a href="products.php" class="nav-link <?php echo active('products.php'); ?>">Productos</a></li>
    <li><a href="ventas.php" class="nav-link <?php echo active('ventas.php'); ?>">Ventas</a></li>
    <li><a href="purchases.php" class="nav-link <?php echo active('purchases.php'); ?>">Compras</a></li>
    <li><a href="suppliers.php" class="nav-link <?php echo active('suppliers.php'); ?>">Proveedores</a></li>
    <li><a href="inventory.php" class="nav-link <?php echo active('inventory.php'); ?>">Inventario</a></li>
    <li><a href="reports.php" class="nav-link <?php echo active('reports.php'); ?>">Reportes</a></li>
    <li><a href="users.php" class="nav-link <?php echo active('users.php'); ?>">Usuarios</a></li>
    <li><a href="departments.php" class="nav-link <?php echo active('departments.php'); ?>">Departamentos</a></li>
    <li><a href="devoluciones.php" class="nav-link <?php echo active('devoluciones.php'); ?>">Devoluciones</a></li>
    <li><a href="audit.php" class="nav-link <?php echo active('audit.php'); ?>">Auditoria</a></li>
    <li><a href="cashregister.php" class="nav-link <?php echo active('cashregister.php'); ?>">Caja</a></li>
    <li><a href="pos.php" class="nav-link <?php echo active('pos.php'); ?>">Punto de Venta</a></li>
    <li><a href="facturacion.php" class="nav-link <?php echo active('facturacion.php'); ?>">Facturacion</a></li>
  </ul>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://via.placeholder.com/32" alt="avatar" class="rounded-circle me-2">
      <strong>Admin</strong>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
      <li><a class="dropdown-item" href="javascript:void(0)" data-action="coming-soon">Perfil</a></li>
      <li><a class="dropdown-item" href="index.php">Cerrar sesion</a></li>
    </ul>
  </div>
</nav>
