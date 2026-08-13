# 🚀 Referencia Rápida - Sistema de Permisos

## ⚡ Verificación Rápida (30 segundos)

```sql
-- En tu cliente MySQL:
SELECT id_rol, nombre FROM roles;
```

Deberías ver:
```
1 | Administrador
2 | Cajero
3 | Bodeguero
```

## 🔑 Funciones Clave

### Verificar Acceso
```php
if (verificarAcceso('productos')) {
    // Usuario tiene acceso al módulo "productos"
}

if (verificarAcceso('productos', 'crear')) {
    // Usuario puede crear productos
}
```

### Proteger Página
```php
require_once 'config/permisos.php';
requerirAcceso('nombre_modulo');  // Redirige si no tiene acceso
```

### Proteger Acción
```php
require_once 'config/permisos.php';
requerirAccesoAccion('nombre_modulo', 'crear');  // Redirige si no puede crear
```

## 📋 Matriz de Permisos

| Módulo | Admin | Cajero | Bodeguero |
|--------|:-----:|:------:|:---------:|
| inicio | ✅ | ✅ | ✅ |
| productos | CRUD | R | CRE |
| clientes | CRUD | CR | ❌ |
| ventas | CRUD | CR | ❌ |
| compras | CRUD | ❌ | CR |
| proveedores | CRUD | ❌ | CRE |
| usuarios | CRUD | ❌ | ❌ |
| caja | ✅ | ✅ | ❌ |
| inventario | ✅ | ❌ | ✅ |
| reportes | ✅ | ❌ | ❌ |

**Leyenda:** CRUD=Crear/Leer/Editar/Eliminar, CR=Crear/Leer, CRE=Crear/Leer/Editar, R=Leer

## 🔧 Modificar Permisos

### 1. Dar permiso a Cajero para editar clientes

Archivo: `config/permisos.php`

Buscar:
```php
ROLE_CAJERO => [
    'clientes' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
```

Cambiar a:
```php
ROLE_CAJERO => [
    'clientes' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
```

### 2. Dar acceso a Bodeguero a reportes

Buscar en `ROLE_BODEGUERO`:
```php
'reportes' => ???  // No existe
```

Agregar:
```php
'reportes' => true,
```

### 3. Crear nuevo rol (ejemplo: Gerente)

Editar `config/permisos.php`:

```php
const ROLE_GERENTE = 4;  // Agregar constante

// Agregar en $PERMISOS_POR_ROL
ROLE_GERENTE => [
    'inicio' => true,
    'productos' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
    'clientes' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
    'ventas' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
    // ... resto de módulos
],
```

Luego en BD:
```sql
INSERT INTO roles (id_rol, nombre, descripcion) 
VALUES (4, 'Gerente', 'Gerente de tienda');
```

## 📂 Estructura de Archivos

```
config/
├── permisos.php       ← 🔴 PRINCIPAL (matriz de permisos)
├── acciones.php       ← Helper para CRUD
└── mensajes.php       ← Mensajes de error

menu/
└── _layout_sidebar.php ← Menú dinámico

*.php               ← Todas las páginas incluyen:
                      require_once 'config/permisos.php';
                      requerirAcceso('modulo');

DOCUMENTACION_PERMISOS.md     ← Guía completa
GUIA_INICIO_RAPIDO.md         ← Tests y setup
verificador.php               ← Verificación del sistema
```

## 🧪 Tests Básicos

### Test 1: Cajero intenta acceder a Usuarios
```
1. Login como Cajero
2. Abre: http://localhost/supermercado-main/usuarios.php
3. Resultado esperado: Redirigido a inicio.php con mensaje de error
```

### Test 2: Bodeguero intenta crear producto
```
1. Login como Bodeguero
2. En Productos → Nuevo Producto → Completa el formulario
3. Resultado esperado: Producto creado (tiene permiso 'crear')
```

### Test 3: Cajero intenta eliminar producto
```
1. Login como Cajero
2. En Productos → Botón "Eliminar"
3. Resultado esperado: Redirigido o mensaje de error (NO tiene permiso 'eliminar')
```

## 🎯 Casos de Uso Comunes

### Agregar nuevo módulo "Devoluciones Extra"
1. Agregar en `config/permisos.php`:
   ```php
   'devoluciones_extra' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
   ```
   Para cada rol que deba tener acceso

2. En la página `devoluciones_extra.php`:
   ```php
   require_once 'config/permisos.php';
   requerirAcceso('devoluciones_extra');
   ```

3. En `menu/_layout_sidebar.php`:
   ```php
   <?php if (verificarAcceso('devoluciones_extra')): ?>
   <li><a href="devoluciones_extra.php">Devoluciones Extra</a></li>
   <?php endif; ?>
   ```

### Dar acceso total a Cajero (convertir en Admin)
Cambiar en `config/permisos.php` en `ROLE_CAJERO`:
```php
// En lugar de lista de módulos, agregar acceso a todo:
// Copiar la lista completa de ROLE_ADMIN
```

O simplemente cambiar el rol en la BD:
```sql
UPDATE usuarios SET fk_rol = 1 WHERE fk_rol = 2;  -- Todos los cajeros se vuelven admins
```

## 🚨 Solucionar Problemas

### "Acceso denegado" aparece pero no debería
1. Verifica `config/permisos.php` - ¿El permiso es `true`?
2. Verifica el ID del rol en la BD vs. las constantes
3. Cierra sesión y abre nuevamente

### Menú sigue mostrando todos los módulos
1. Recarga la página (Ctrl+Shift+R para limpiar caché)
2. Cierra sesión y abre nuevamente
3. Verifica que `menu/_layout_sidebar.php` tenga `require_once 'config/permisos.php';`

### Las funciones no se encuentran
1. Verifica que el archivo `config/permisos.php` exista
2. Verifica la ruta: debe ser `config/permisos.php` (sin barra inicial)
3. Verifica que no haya errores PHP antes de `require_once`

## 📞 Archivos de Ayuda

| Necesitas | Lee |
|-----------|-----|
| Empezar rápido | GUIA_INICIO_RAPIDO.md |
| Entender todo | DOCUMENTACION_PERMISOS.md |
| Ver qué cambió | RESUMEN_CAMBIOS.md |
| Modificar BD | SQL_ROLES.sql |
| Verificar instalación | verificador.php |
| Crear nuevo módulo | Esta hoja (Casos de Uso) |

## 💾 Guardar Cambios Permanentes

Cualquier cambio que hagas en `config/permisos.php` es inmediato (sin reiniciar).

Pero si cambias IDs de roles en la BD, necesitas actualizar:
1. `config/permisos.php` (constantes)
2. `usuarios.fk_rol` (en BD)

## 🔐 Seguridad

La seguridad es en **servidor**, no en cliente:
- ✅ Verificación en backend
- ✅ No se puede bypasear desde JavaScript
- ✅ Redirección automática si no tiene permiso
- ✅ Sesión requerida

## 📊 Dashboard de Estado

Para ver el estado del sistema:
1. Inicia sesión
2. Abre: `verificador.php`
3. Verifica que todos los checks estén ✅

---

**Última actualización: 2026-08-13**

