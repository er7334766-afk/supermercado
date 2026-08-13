# 📧 Configuración de Recuperación de Contraseña

## Resumen de Cambios Implementados

La funcionalidad de recuperación de contraseña ha sido completamente implementada en tu sistema de supermercado. Se modificaron y crearon los siguientes archivos:

### Archivos Modificados:
- **index.php** - Se agregó enlace "¿Olvidaste tu contraseña?" debajo del botón Entrar
- **recuperar_contrasena.php** - Completamente reescrito con validaciones robustas y flujo de 3 pasos

### Archivos Creados:
- **config/mailer.php** - Configuración centralizada para envío de correos
- **SQL_RECUPERACION_CONTRASENA.sql** - Script SQL para crear las columnas necesarias

---

## 🔧 Paso 1: Ejecutar el Script SQL

Antes de usar la funcionalidad, ejecuta el siguiente script SQL en tu base de datos:

```sql
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(255) NULL DEFAULT NULL AFTER `estado`;
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `reset_expires` DATETIME NULL DEFAULT NULL AFTER `reset_token`;
ALTER TABLE `usuarios` ADD INDEX IF NOT EXISTS `idx_reset_token` (`reset_token`);
```

**Opción 1: Desde phpMyAdmin**
1. Abre phpMyAdmin en tu navegador (http://localhost/phpmyadmin)
2. Selecciona la base de datos `p_supermercado`
3. Ve a la pestaña "SQL"
4. Copia y pega el script
5. Haz clic en "Continuar"

**Opción 2: Desde la terminal MySQL**
```bash
mysql -u root p_supermercado < SQL_RECUPERACION_CONTRASENA.sql
```

---

## 📬 Paso 2: Configurar Envío de Correos

Abre `config/mailer.php` y selecciona una de estas opciones:

### 🌟 OPCIÓN 1: Gmail (Recomendado para Desarrollo)

#### Pasos:
1. **Habilita autenticación de dos factores en tu cuenta de Google**
   - Ve a https://myaccount.google.com/
   - Selecciona "Seguridad" en el menú lateral
   - Activa "Verificación en dos pasos"

2. **Genera una contraseña de aplicación**
   - Vuelve a "Seguridad"
   - Busca "Contraseñas de aplicación" (solo aparece si tienes 2FA activado)
   - Selecciona "Correo" y "Windows Computer"
   - Google te generará una contraseña de 16 caracteres (sin espacios)

3. **Modifica `config/mailer.php`:**
   ```php
   $mailConfig = [
       'method' => 'smtp',
       'smtp_host' => 'smtp.gmail.com',
       'smtp_port' => 587,
       'smtp_secure' => 'tls',
       'smtp_user' => 'tu-email@gmail.com',        // Tu email de Google
       'smtp_pass' => 'abcd efgh ijkl mnop',      // Contraseña de app generada
       'from_email' => 'tu-email@gmail.com',
       'from_name' => 'Sistema Supermercado',
       // ... resto de configuración
   ];
   ```

4. **Prueba la funcionalidad:**
   - Ve a http://localhost/supermercado-main/index.php
   - Haz clic en "¿Olvidaste tu contraseña?"
   - Ingresa el correo de un usuario
   - Deberías recibir un email con el enlace de recuperación

---

### 💼 OPCIÓN 2: Servidor SMTP Corporativo/Externo

Si tienes un servidor SMTP propio (como Outlook, servidor corporativo, etc.):

```php
$mailConfig = [
    'method' => 'smtp',
    'smtp_host' => 'mail.tudominio.com',      // Host SMTP
    'smtp_port' => 587,                        // Puerto (587 TLS, 465 SSL)
    'smtp_secure' => 'tls',                    // 'tls' o 'ssl'
    'smtp_user' => 'usuario@tudominio.com',   // Usuario SMTP
    'smtp_pass' => 'tu-contraseña',           // Contraseña
    'from_email' => 'noreply@tudominio.com',  // Email remitente
    'from_name' => 'Sistema Supermercado',
    // ... resto
];
```

---

### 📧 OPCIÓN 3: Función mail() de PHP (No Recomendado)

Si no puedes configurar SMTP, la aplicación intentará usar `mail()` automáticamente como fallback. Sin embargo, esto NO es recomendado porque:
- Los emails pueden ir a spam
- No hay autenticación
- Requiere que el servidor PHP tenga mail configurado

Para usar mail() de todas formas:

```php
$mailConfig = [
    'method' => 'mail',  // Solo cambiar esto
    'from_email' => 'noreply@tudominio.com',
    'from_name' => 'Sistema Supermercado',
    // ... resto
];
```

---

### 🚀 OPCIÓN 4: PHPMailer Avanzado (Opcional)

Si instalaste PHPMailer mediante Composer:

```bash
composer require phpmailer/phpmailer
```

La aplicación lo detectará automáticamente. `config/mailer.php` ya tiene soporte para PHPMailer.

---

## 🧪 Modo Desarrollo/Testing

Si **aún no has configurado SMTP**, la aplicación mostrará automáticamente el enlace de recuperación en pantalla para que puedas probarlo localmente sin necesidad de correo real:

```
Nota para desarrollo: El correo no se envió (configura SMTP en config/mailer.php). 
Usa este enlace para pruebas: http://localhost/supermercado-main/recuperar_contrasena.php?token=abc123...
```

---

## 🔐 Características de Seguridad Implementadas

✅ **Tokens seguros**: Generados con `random_bytes(32)` (64 caracteres hexadecimales)
✅ **Expiración**: Tokens válidos por 1 hora (configurable en `config/mailer.php`)
✅ **Contraseña hasheada**: Utiliza `password_hash()` con `PASSWORD_DEFAULT` (bcrypt)
✅ **Validaciones**:
   - Confirmar contraseña (dos campos deben coincidir)
   - Mínimo 6 caracteres
   - Email válido
   - Token no reutilizable (se elimina después de usar)
✅ **Consultas preparadas**: Protección contra SQL injection con PDO
✅ **Mensajes de seguridad**: No revela si un correo existe en el sistema

---

## 📋 Flujo de Uso

### Para el Usuario:
1. En la pantalla de login, hace clic en "¿Olvidaste tu contraseña?"
2. Ingresa su correo electrónico
3. Recibe un email con un enlace de recuperación (válido 1 hora)
4. Hace clic en el enlace
5. Ingresa su nueva contraseña (2 veces para confirmar)
6. Sistema valida, actualiza la contraseña y redirige al login
7. Inicia sesión con su nueva contraseña

### Base de Datos:
- Los tokens se almacenan en `usuarios.reset_token` (VARCHAR 255)
- La fecha de expiración en `usuarios.reset_expires` (DATETIME)
- Una vez usado, ambos campos se limpian (NULL)

---

## 🛠️ Solución de Problemas

### "El correo no llega"

1. **Verificar configuración:**
   - ¿Host SMTP correcto? (ej: smtp.gmail.com)
   - ¿Puerto correcto? (587 TLS, 465 SSL)
   - ¿Credenciales correctas?

2. **Si usas Gmail:**
   - ¿Generaste contraseña de aplicación? (no la contraseña normal de Google)
   - ¿Está habilitada la verificación en dos pasos?
   - Prueba con la contraseña de app sin espacios

3. **Ver logs de error:**
   - Abre `config/mailer.php`
   - Los errores se registran con `error_log()`
   - Revisa el log de errores de PHP (generalmente en `error_log` del servidor)

### "Token expirado"

- Los tokens expiran después de 1 hora
- Modifica `'token_expiry_hours' => 1` en `config/mailer.php` para cambiar la duración
- El usuario puede solicitar un nuevo enlace

### "Error de conexión SMTP"

- Asegúrate de que el servidor SMTP sea accesible desde tu máquina
- Algunos firewall corporativos pueden bloquear SMTP
- Intenta con puertos alternativos (587, 465)

---

## 📝 Cambios en Archivos Existentes

### index.php
```php
// Se agregó este bloque después del formulario:
<div class="text-center mt-3">
    <small>
        <a href="recuperar_contrasena.php" class="text-muted text-decoration-none">
            ¿Olvidaste tu contraseña?
        </a>
    </small>
</div>
```

### recuperar_contrasena.php
Completamente reescrito con:
- Validación de correo
- Generación segura de tokens
- Formulario para cambiar contraseña con confirmación
- Validación de expiración de token con DateTime
- Mensajes claros de éxito/error
- Soporte para desarrollo local (muestra enlace si no hay SMTP)

---

## 📚 Referencia Rápida

| Archivo | Propósito | Modificado |
|---------|-----------|-----------|
| index.php | Login principal | ✏️ Modificado |
| recuperar_contrasena.php | Flujo de recuperación | ✏️ Reescrito |
| config/mailer.php | Configuración de correos | ✨ Creado |
| SQL_RECUPERACION_CONTRASENA.sql | Script SQL | ✨ Creado |

---

## 🎯 Próximos Pasos Sugeridos

1. Ejecutar el SQL en tu base de datos
2. Configurar `config/mailer.php` con tus credenciales SMTP
3. Crear un usuario de prueba con un correo válido
4. Probar el flujo completo de recuperación
5. Ajustar la duración de expiración del token si es necesario

---

## 📞 Soporte

Si encuentras problemas:

1. Verifica que las columnas se agregaron a la tabla `usuarios`
2. Comprueba que `config/mailer.php` tiene las credenciales correctas
3. Revisa los logs de error de PHP
4. Usa el modo desarrollo para ver el enlace en pantalla

¡La implementación está lista para producción! 🚀
