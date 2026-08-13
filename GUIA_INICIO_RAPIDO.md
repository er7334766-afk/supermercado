# Guía de Inicio Rápido - Sistema de Permisos

## ✅ Verificación Inicial (5 minutos)

### Paso 1: Verificar Roles en Base de Datos

Ejecuta esta consulta en tu base de datos para verificar que los roles tengan los IDs correctos:

```sql
SELECT id_rol, nombre FROM roles;
```

**Resultado esperado:**
```
id_rol | nombre
-------|-------------------
1      | Administrador
2      | Cajero
3      | Bodeguero
```

**⚠️ Si los IDs son diferentes:**
- Abre `config/permisos.php`
- Actualiza las constantes al inicio:
  ```php
  const ROLE_ADMIN = 1;      // Cambiar al ID correcto del Administrador
  const ROLE_CAJERO = 2;     // Cambiar al ID correcto del Cajero
  const ROLE_BODEGUERO = 3;  // Cambiar al ID correcto del Bodeguero
  ```

### Paso 2: Verificar Usuarios en Base de Datos

Ejecuta esta consulta para verificar que tengas usuarios con roles:

```sql
SELECT u.usuario, r.nombre as rol FROM usuarios u LEFT JOIN roles r ON r.id_rol = u.fk_rol;
```

**Deberías ver algo como:**
```
usuario      | rol
-------------|-------------------
admin        | Administrador
cajero_01    | Cajero
bodeguero_01 | Bodeguero
```

## 🧪 Pruebas del Sistema

### Test 1: Acceso como Administrador
1. Inicia sesión con un usuario Administrador
2. Espera ver el menú con TODOS los módulos:
   - ✅ Inicio, Productos, Clientes, Ventas, Compras
   - ✅ Proveedores, Caja, Usuarios, Inventario, Reportes
   - ✅ Departamentos, Devoluciones, Auditoria, Punto de Venta, Facturación
3. Intenta crear, editar y eliminar en cualquier módulo - debe funcionar
4. **Resultado esperado:** Todo funciona sin restricciones

### Test 2: Acceso como Cajero
1. Inicia sesión con un usuario Cajero
2. Verifica el menú - deberías ver solo:
   - ✅ Inicio
   - ✅ Productos
   - ✅ Clientes
   - ✅ Ventas
   - ✅ Caja
   - ❌ NO deberías ver: Usuarios, Proveedores, Compras, Inventario, etc.

3. Pruebas de acceso directo:
   - Abre nuevo navegador con dirección: `http://localhost/supermercado-main/usuarios.php`
   - **Resultado esperado:** Deberías ser redirigido a inicio con mensaje "Acceso denegado"

4. Prueba de productos:
   - En módulo Productos: deberías poder VER los productos
   - Deberías poder crear productos (si tienes permisos para crear)
   - Pero NO deberías poder editar ni eliminar
   - **Si ves botones "Editar" o "Eliminar":** Son deshabilitados en el backend (seguridad extra)

5. **Resultado esperado:** Acceso limitado a solo módulos de venta

### Test 3: Acceso como Bodeguero
1. Inicia sesión con un usuario Bodeguero
2. Verifica el menú - deberías ver solo:
   - ✅ Inicio
   - ✅ Productos
   - ✅ Proveedores
   - ✅ Compras
   - ✅ Inventario
   - ❌ NO deberías ver: Usuarios, Ventas, Caja, Reportes, etc.

3. Pruebas de acceso directo:
   - Abre: `http://localhost/supermercado-main/ventas.php`
   - **Resultado esperado:** Deberías ser redirigido a inicio

4. Prueba de acciones:
   - En Productos: deberías poder crear y editar
   - Pero NO deberías poder eliminar
   - En Proveedores: igual que productos (crear, editar, NO eliminar)
   - En Compras: solo ver y crear (NO editar ni eliminar)

5. **Resultado esperado:** Acceso limitado a módulos de inventario

## 🔧 Personalización Rápida

### Dar permiso extra a Cajero (ejemplo: editar clientes)

1. Abre `config/permisos.php`
2. Busca la sección `ROLE_CAJERO`
3. Encuentra la línea:
   ```php
   'clientes' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
   ```
4. Cambia `'editar' => false` a `'editar' => true`
5. Guarda el archivo
6. El cambio es inmediato (sin necesidad de reiniciar)

### Agregar nuevo rol (ejemplo: Gerente)

1. Abre `config/permisos.php`
2. Agrega una nueva constante:
   ```php
   const ROLE_GERENTE = 4;
   ```
3. Agrega un nuevo rol en el array `$PERMISOS_POR_ROL`:
   ```php
   ROLE_GERENTE => [
       'inicio' => true,
       'productos' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => true],
       'ventas' => ['ver' => true, 'crear' => false, 'editar' => false, 'eliminar' => false],
       // ... otros módulos ...
   ],
   ```
4. Agrega el nuevo rol en la tabla `roles` de tu base de datos
5. Asigna usuarios al nuevo rol

### Permitir que Bodeguero edite compras

1. Abre `config/permisos.php`
2. Busca `ROLE_BODEGUERO`
3. Cambia:
   ```php
   'compras' => ['ver' => true, 'crear' => true, 'editar' => false, 'eliminar' => false],
   ```
   Por:
   ```php
   'compras' => ['ver' => true, 'crear' => true, 'editar' => true, 'eliminar' => false],
   ```

## 📋 Checklist de Validación

- [ ] Verificaste que los IDs de roles coinciden con los de tu BD
- [ ] Probaste login como Administrador y ves todos los módulos
- [ ] Probaste login como Cajero y ves solo módulos permitidos
- [ ] Probaste login como Bodeguero y ves solo módulos permitidos
- [ ] Intentaste acceder directamente a URL de módulo no permitido y fuiste redirigido
- [ ] Intentaste crear, editar, eliminar en módulo sin permiso y fuiste bloqueado
- [ ] Leíste `DOCUMENTACION_PERMISOS.md` para entender el sistema
- [ ] Configuraste los permisos según tus necesidades de negocio

## 🚨 Si Algo No Funciona

### Problema: El menú sigue mostrando todos los módulos

**Solución:**
1. Verifica que `menu/_layout_sidebar.php` tenga:
   ```php
   require_once 'config/permisos.php';
   ```
2. Verifica que cada opción de menú tenga:
   ```php
   <?php if (verificarAcceso('nombre_modulo')): ?>
   ```

### Problema: No se redirige cuando accedo a módulo no permitido

**Solución:**
1. Verifica que la página tenga:
   ```php
   require_once 'config/permisos.php';
   requerirAcceso('nombre_modulo');
   ```
2. Verifica que `$_SESSION['usuario_rol']` tenga un valor numérico

### Problema: No veo mensaje de acceso denegado

**Solución:**
1. Verifica que `inicio.php` tenga:
   ```php
   require_once 'config/mensajes.php';
   $mensajeAccesoDenegado = mostrarMensajeAccesoDenegado();
   ```
2. Verifica que la página muestre el mensaje:
   ```php
   <?php if (!empty($mensajeAccesoDenegado)) : ?>
       <div class="alert alert-warning">...</div>
   <?php endif; ?>
   ```

## 📞 Soporte

Si tienes dudas sobre:
- **Cómo funcionan los permisos:** Lee `DOCUMENTACION_PERMISOS.md`
- **Qué cambios se realizaron:** Lee `RESUMEN_CAMBIOS.md`
- **Estructura de roles en BD:** Lee `SQL_ROLES.sql`
- **Cómo modificar permisos:** Ver sección "Personalización Rápida" en esta guía

