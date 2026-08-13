<?php
require_once 'config/permisos.php';

$current = basename($_SERVER['SCRIPT_NAME']);
function active($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>
<nav class="sidebar d-flex flex-column p-3">
  <a href="inicio.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <span class="fs-4">Supermercado Victoria</span>
  </a>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <ul class="nav nav-pills flex-column mb-auto">
    <!-- Inicio: Acceso para todos los roles -->
    <?php if (verificarAcceso('inicio')): ?>
    <li class="nav-item"><a href="inicio.php" class="nav-link <?php echo active('inicio.php'); ?>">Inicio</a></li>
    <?php endif; ?>

    <!-- Clientes -->
    <?php if (verificarAcceso('clientes')): ?>
    <li><a href="clientes.php" class="nav-link <?php echo active('clientes.php'); ?>">Clientes</a></li>
    <?php endif; ?>

    <!-- Productos -->
    <?php if (verificarAcceso('productos')): ?>
    <li><a href="productos.php" class="nav-link <?php echo active('productos.php'); ?>">Productos</a></li>
    <?php endif; ?>

    <!-- Ventas -->
    <?php if (verificarAcceso('ventas')): ?>
    <li><a href="ventas.php" class="nav-link <?php echo active('ventas.php'); ?>">Ventas</a></li>
    <?php endif; ?>

    <!-- Compras -->
    <?php if (verificarAcceso('compras')): ?>
    <li><a href="compras.php" class="nav-link <?php echo active('compras.php'); ?>">Compras</a></li>
    <?php endif; ?>

    <!-- Proveedores -->
    <?php if (verificarAcceso('proveedores')): ?>
    <li><a href="proveedores.php" class="nav-link <?php echo active('proveedores.php'); ?>">Proveedores</a></li>
    <?php endif; ?>

    <!-- Inventario -->
    <?php if (verificarAcceso('inventario')): ?>
    <li><a href="inventario.php" class="nav-link <?php echo active('inventario.php'); ?>">Inventario</a></li>
    <?php endif; ?>

    <!-- Reportes -->
    <?php if (verificarAcceso('reportes')): ?>
    <li><a href="reportes.php" class="nav-link <?php echo active('reportes.php'); ?>">Reportes</a></li>
    <?php endif; ?>

    <!-- Usuarios: Solo Administrador -->
    <?php if (verificarAcceso('usuarios')): ?>
    <li><a href="usuarios.php" class="nav-link <?php echo active('usuarios.php'); ?>">Usuarios</a></li>
    <?php endif; ?>

    <!-- Departamentos -->
    <?php if (verificarAcceso('departamentos')): ?>
    <li><a href="departamentos.php" class="nav-link <?php echo active('departamentos.php'); ?>">Departamentos</a></li>
    <?php endif; ?>

    <!-- Devoluciones -->
    <?php if (verificarAcceso('devoluciones')): ?>
    <li><a href="devoluciones.php" class="nav-link <?php echo active('devoluciones.php'); ?>">Devoluciones</a></li>
    <?php endif; ?>

    <!-- Auditoria -->
    <?php if (verificarAcceso('auditoria')): ?>
    <li><a href="auditoria.php" class="nav-link <?php echo active('auditoria.php'); ?>">Auditoria</a></li>
    <?php endif; ?>

    <!-- Caja -->
    <?php if (verificarAcceso('caja')): ?>
    <li><a href="caja.php" class="nav-link <?php echo active('caja.php'); ?>">Caja</a></li>
    <?php endif; ?>

    <!-- Punto de Venta -->
    <?php if (verificarAcceso('punto_venta')): ?>
    <li><a href="punto_venta.php" class="nav-link <?php echo active('punto_venta.php'); ?>">Punto de Venta</a></li>
    <?php endif; ?>

    <!-- Facturación -->
    <?php if (verificarAcceso('facturacion')): ?>
    <li><a href="facturacion.php" class="nav-link <?php echo active('facturacion.php'); ?>">Facturacion</a></li>
    <?php endif; ?>
  </ul>
  <hr style="border-color: rgba(255,255,255,0.06)">
  <div class="mt-auto pb-3">
    <a href="logout.php" class="d-flex align-items-center text-white text-decoration-none">
      <span class="me-2">Salir</span>
    </a>
  </div>
</nav>
