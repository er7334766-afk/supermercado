<?php
/**
 * Configuración de PHPMailer para envío de correos de recuperación de contraseña
 * 
 * OPCIONES DE ENVÍO:
 * 1. Gmail (recomendado para desarrollo)
 * 2. Servidor SMTP local/externo
 * 3. Función mail() de PHP (no recomendado, puede ir a spam)
 */

// Configurar según tu proveedor de correo
$mailConfig = [
    // Opción 1: Gmail (requiere contraseña de aplicación)
    'method' => 'smtp', // 'smtp', 'mail', 'sendmail'
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // 'tls' o 'ssl'
    'smtp_user' => 'tu-email@gmail.com', // Cambiar por tu email
    'smtp_pass' => 'tu-contraseña-app', // Cambiar por contraseña de app (NO la contraseña normal de Gmail)
    'from_email' => 'tu-email@gmail.com', // Cambiar
    'from_name' => 'Sistema Supermercado',
    
    // Opción 2: Servidor SMTP local/externo (descomenta y configura si lo prefieres)
    // 'smtp_host' => 'mail.tudominio.com',
    // 'smtp_port' => 587,
    // 'smtp_secure' => 'tls',
    // 'smtp_user' => 'usuario@tudominio.com',
    // 'smtp_pass' => 'contraseña',
    
    // Configuración general
    'link_base_url' => (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']),
    'token_expiry_hours' => 1, // Horas de validez del token
];

/**
 * Envía correo de recuperación de contraseña
 * 
 * @param string $emailDestino Email del usuario
 * @param string $nombreUsuario Nombre del usuario
 * @param string $token Token de recuperación
 * @return bool True si se envió correctamente, False en caso contrario
 */
function enviarCorreoRecuperacion($emailDestino, $nombreUsuario, $token) {
    global $mailConfig;
    
    // URL de recuperación
    $enlaceRecuperacion = $mailConfig['link_base_url'] . '/recuperar_contrasena.php?token=' . urlencode($token);
    
    // Cuerpo del correo en HTML
    $asunto = 'Recuperación de Contraseña - Sistema Supermercado';
    $cuerpoHtml = "
    <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 5px; }
                h2 { color: #333; }
                .info { color: #666; font-size: 14px; }
                .button { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin-top: 20px; }
                .footer { color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>¡Hola, $nombreUsuario!</h2>
                <p>Recibimos una solicitud para recuperar tu contraseña en el Sistema Supermercado.</p>
                <p class='info'>
                    <strong>Este enlace vence en " . $mailConfig['token_expiry_hours'] . " hora(s).</strong><br>
                    Si no solicitaste esto, puedes ignorar este mensaje.
                </p>
                <a href='$enlaceRecuperacion' class='button'>Recuperar Contraseña</a>
                <p>O copia y pega este enlace en tu navegador:</p>
                <p class='info'>$enlaceRecuperacion</p>
                <div class='footer'>
                    <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
                    <p>© " . date('Y') . " Sistema Supermercado. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
    </html>
    ";
    
    // Intentar enviar con diferentes métodos
    switch ($mailConfig['method']) {
        case 'smtp':
            return enviarPorSMTP($emailDestino, $asunto, $cuerpoHtml);
        
        case 'mail':
            return enviarPorMail($emailDestino, $asunto, $cuerpoHtml);
        
        case 'sendmail':
            return enviarPorSendmail($emailDestino, $asunto, $cuerpoHtml);
        
        default:
            return false;
    }
}

/**
 * Envía correo usando SMTP (recomendado)
 * Requiere: composer require phpmailer/phpmailer
 */
function enviarPorSMTP($emailDestino, $asunto, $cuerpoHtml) {
    global $mailConfig;
    
    try {
        // Verificar si PHPMailer está instalado
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Si no está instalado, intentar usar mail() como fallback
            return enviarPorMail($emailDestino, $asunto, $cuerpoHtml);
        }
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $mailConfig['smtp_host'];
        $mail->Port = $mailConfig['smtp_port'];
        $mail->SMTPSecure = $mailConfig['smtp_secure'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig['smtp_user'];
        $mail->Password = $mailConfig['smtp_pass'];
        
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($emailDestino);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpoHtml;
        $mail->AltBody = strip_tags($cuerpoHtml);
        $mail->CharSet = 'UTF-8';
        
        return $mail->send();
        
    } catch (Exception $e) {
        // Fallback a mail()
        error_log('Error SMTP: ' . $e->getMessage());
        return enviarPorMail($emailDestino, $asunto, $cuerpoHtml);
    }
}

/**
 * Envía correo usando la función mail() de PHP
 */
function enviarPorMail($emailDestino, $asunto, $cuerpoHtml) {
    global $mailConfig;
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $mailConfig['from_email'] . "\r\n";
    $headers .= "Reply-To: " . $mailConfig['from_email'] . "\r\n";
    
    return mail($emailDestino, $asunto, $cuerpoHtml, $headers);
}

/**
 * Envía correo usando sendmail
 */
function enviarPorSendmail($emailDestino, $asunto, $cuerpoHtml) {
    global $mailConfig;
    
    // Usar la función mail() con sendmail
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $mailConfig['from_email'] . "\r\n";
    
    return mail($emailDestino, $asunto, $cuerpoHtml, $headers);
}

?>
