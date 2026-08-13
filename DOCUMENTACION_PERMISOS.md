# Sistema de Control de Acceso y Permisos - Documentación

## Descripción General

Se ha implementado un sistema de control de acceso centralizado basado en roles. Este sistema controla qué módulos y qué acciones puede realizar cada usuario según su rol.

## Roles Configurados

### 1. Administrador (ID: 1)
- **Acceso completo** a todos los módulos del sistema
- Puede crear, consultar, editar y eliminar registros en todos los módulos
- **Módulos disponibles:**
  - Inicio/Dashboard
  - Productos (CRUD completo)
  - Clientes (CRUD completo)
  - Proveedores (CRUD completo)
  - Compras (CRUD completo)
  - Ventas (CRUD completo)
  - Caja
  - Usuarios (CRUD completo)
  - Reportes
  - Departamentos
  - Devoluciones
  - Auditoría
  - Inventario
  - Punto de Venta
  - Facturación

### 2. Cajero (ID: 2)
- **Acceso limitado** a módulos de operación de ventas
- **Módulos disponibles:**
  - Inicio/Dashboard
  - Productos (Solo lectura - ver precios y existencias)
  - Clientes (Ver y registrar)
  - Ventas (Ver y registrar)
  - Caja
- **Restricciones:**
  - NO puede crear, editar ni eliminar productos
  - NO puede acceder a usuarios, proveedores, compras, departamentos, devoluciones, auditoría, inventario, punto de venta, facturación

### 3. Bodeguero (ID: 3)
- **Acceso limitado** a módulos de inventario y compras
- **Módulos disponibles:**
  - Inicio/Dashboard
  - Productos (Ver, registrar y editar)
  - Proveedores (Ver, registrar y editar)
  - Compras (Ver y registrar)
  - Inventario
- **Restricciones:**
  - NO puede eliminar productos ni proveedores
  - NO puede acceder a usuarios, ventas, caja, departamentos, devoluciones, auditoría, reportes, punto de venta, facturación

## Estructura de Archivos

### Archivos de Configuración

#### `config/permisos.php`
Archivo principal del sistema de permisos. Contiene:
- Definiciones de roles (constantes)
- Array `$PERMISOS_POR_ROL` con la configuración de permisos
- Funciones principales:
  - `verificarAcceso($modulo, $accion = null)` - Verifica si el usuario tiene acceso
  - `requerirAcceso($modulo, $accion = null)` - Redirige si no tiene acceso
  - `obtenerPermisosRolActual()` - Obtiene permisos del rol actual
  - `obtenerNombreRol($rolId)` - Retorna el nombre del rol

#### `config/acciones.php`
Contiene funciones helper para validar acciones específicas:
- `requerirAccesoAccion($modulo, $accion)` - Valida acceso a una acción específica

#### `config/mensajes.php`
Maneja mensajes de acceso denegado:
- `mostrarMensajeAccesoDenegado()` - Obtiene y limpia mensajes de sesión

### Menú Dinámico

#### `menu/_layout_sidebar.php`
El menú ha sido actualizado para mostrar solo los módulos disponibles según el rol del usuario actual. Cada enlace está protegido con:
```php
<?php if (verificarAcceso('nombre_modulo')): ?>
    <li><a href="modulo.php" class="nav-link">Módulo</a></li>
<?php endif; ?>
```

## Cómo Funciona el Sistema

### 1. Verificación al Cargar una Página

Cada página protegida incluye en la parte superior:

```php
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

// Verificar acceso al módulo
requerirAcceso('nombre_modulo');
```

### 2. Verificación de Acciones Específicas

Para operaciones CRUD (crear, editar, eliminar):

```php
<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

// Verificar acceso a una acción específica
requerirAccesoAccion('nombre_modulo', 'crear');
```

### 3. Flujo de Seguridad

1. El usuario inicia sesión en `index.php`
2. Se guarda en sesión: `$_SESSION['usuario_rol']` (ID del rol)
3. Al acceder a cualquier página, se verifica:
   - Que el usuario esté autenticado (tenga `usuario_id`)
   - Que su rol tenga permiso para el módulo
   - Que tenga permiso para la acción específica (crear, editar, eliminar, ver)
4. Si no tiene permiso, es redirigido a `inicio.php` con un mensaje de error

## Modificar Permisos

Para modificar los permisos de un rol, editar el archivo `config/permisos.php`:

### Ejemplo: Dar permiso a Cajero para editar clientes

```php
ROLE_CAJERO => [
    'inicio' => true,
    'productos' => ['ver' => true, 'crear' => false, 'editar' => false, 'eliminar' => false],
    'clientes' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false], // Cambiar a true
    'ventas' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
    'caja' => true,
],
```

### Ejemplo: Dar acceso a nuevo módulo

```php
ROLE_BODEGUERO => [
    'inicio' => true,
    'productos' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
    'proveedores' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
    'compras' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
    'inventario' => true,
    'nuevo_modulo' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false], // Nuevo
],
```

## Agregar Protección a Nuevas Páginas

### Para una página de visualización:

```php
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAcceso('nuevo_modulo');

// Resto del código...
```

### Para una página de creación:

```php
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAccesoAccion('nuevo_modulo', 'crear');

// Resto del código...
```

### Para una página de edición:

```php
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/conexion.php';
require_once 'config/permisos.php';

requerirAccesoAccion('nuevo_modulo', 'editar');

// Resto del código...
```

### Para una página de eliminación:

```php
<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/permisos.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

requerirAccesoAccion('nuevo_modulo', 'eliminar');

// Resto del código...
```

## Páginas Protegidas

Las siguientes páginas han sido protegidas con el sistema de permisos:

### Visualización de Módulos
- `inicio.php` - Acceso: todos los roles
- `productos.php` - Acceso: Administrador, Cajero, Bodeguero
- `clientes.php` - Acceso: Administrador, Cajero
- `ventas.php` - Acceso: Administrador, Cajero
- `compras.php` - Acceso: Administrador, Bodeguero
- `proveedores.php` - Acceso: Administrador, Bodeguero
- `usuarios.php` - Acceso: Administrador
- `inventario.php` - Acceso: Administrador, Bodeguero
- `reportes.php` - Acceso: Administrador
- `departamentos.php` - Acceso: Administrador
- `devoluciones.php` - Acceso: Administrador
- `auditoria.php` - Acceso: Administrador
- `caja.php` - Acceso: Administrador, Cajero
- `punto_venta.php` - Acceso: Administrador
- `facturacion.php` - Acceso: Administrador

### Creación de Registros
- `nuevo_cliente.php` - Acción: crear clientes
- `nuevo_producto.php` - Acción: crear productos
- `nuevo_usuario.php` - Acción: crear usuarios
- `nuevo_proveedor.php` - Acción: crear proveedores
- `nueva_venta.php` - Acción: crear ventas
- `nueva_compra.php` - Acción: crear compras

### Edición de Registros
- `editar_cliente.php` - Acción: editar clientes
- `editar_producto.php` - Acción: editar productos
- `editar_usuario.php` - Acción: editar usuarios
- `editar_proveedor.php` - Acción: editar proveedores
- `editar_venta.php` - Acción: editar ventas
- `editar_compra.php` - Acción: editar compras

### Eliminación de Registros
- `eliminar_cliente.php` - Acción: eliminar clientes
- `eliminar_producto.php` - Acción: eliminar productos
- `eliminar_proveedor.php` - Acción: eliminar proveedores

### Guardado de Datos
- `guardar_venta.php` - Acción: crear ventas
- `guardar_compra.php` - Acción: crear compras

## Consideraciones de Seguridad

1. **Validación en servidor:** Todas las verificaciones de acceso se realizan en el servidor (lado servidor)
2. **Sesión requerida:** Cada acceso requiere una sesión activa con `usuario_id` válido
3. **Redirección automática:** Si no hay acceso, se redirige automáticamente a inicio
4. **Constantes de roles:** Los IDs de roles se definen como constantes para evitar errores

## Pruebas Recomendadas

1. Iniciar sesión con usuario Administrador y verificar acceso a todos los módulos
2. Iniciar sesión con usuario Cajero y verificar:
   - Acceso a Inicio, Productos (lectura), Clientes, Ventas, Caja
   - Acceso denegado a Proveedores, Compras, Usuarios, etc.
3. Iniciar sesión con usuario Bodeguero y verificar:
   - Acceso a Inicio, Productos, Proveedores, Compras, Inventario
   - Acceso denegado a Ventas, Caja, Usuarios, etc.
4. Intentar acceder directamente a URLs de módulos no permitidos (debe redirigir)
5. Intentar acceder directamente a formularios de creación sin permiso (debe redirigir)

## Notas Importantes

- El sistema está diseñado para ser **mínimamente invasivo** con el código existente
- Se pueden agregar más roles editando `config/permisos.php`
- La seguridad depende de que `$_SESSION['usuario_rol']` esté correctamente asignado en el login
- Si un usuario es eliminado o su rol cambia, se requiere que cierre y reabra sesión

