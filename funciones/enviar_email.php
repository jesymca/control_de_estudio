<?php
function enviarEmail($email, $nombre, $asunto, $cuerpo) {
    global $footer_correo, $logo, $smtp_host, $smtp_port, $smtp_secure, $smtp_username, $smtp_password, $smtp_from_name, $smtp_bcc, $siglas_institucion, $nombre_sistema;
    
    require_once 'PHPMailer/PHPMailerAutoload.php';
    
    $mail = new PHPMailer();
    
    // Configuración SMTP con variables centralizadas
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = !empty($smtp_secure) ? $smtp_secure : 'tls';
    $mail->Host = !empty($smtp_host) ? $smtp_host : 'smtp.gmail.com';
    $mail->Port = !empty($smtp_port) ? $smtp_port : '587';
    $mail->SMTPDebug = 0; // 0 = off (producción), 2 = solo para pruebas
    
    // Credenciales del remitente
    $mail->Username = !empty($smtp_username) ? $smtp_username : 'hectorlamaquina14@gmail.com';
    $mail->Password = !empty($smtp_password) ? $smtp_password : 'tjml yrrt gcum ulgf';
    
    // Quién envía el correo
    $remitente_nombre = !empty($smtp_from_name) ? $smtp_from_name : (($siglas_institucion ?? 'UPTPC') . ' - ' . ($nombre_sistema ?? 'Control de Estudios'));
    $mail->setFrom($mail->Username, $remitente_nombre);
    
    // Destinatarios
    $mail->addAddress($email, $nombre);
    if (!empty($smtp_bcc)) {
        $mail->addBCC($smtp_bcc, 'Control');
    }
    
    // Configuración del mensaje
    $mail->Encoding = "base64";
    $mail->CharSet = 'utf-8';
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body = ($logo ?? '') . $cuerpo . ($footer_correo ?? '');
    
    // Enviar
    if ($mail->send()) {
        return true;
    } else {
        error_log("Error al enviar email: " . $mail->ErrorInfo);
        return false;
    }
}
?>