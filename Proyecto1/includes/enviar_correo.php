<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga PHPMailer desde la ruta correcta
require __DIR__ . '/../config/PHPMailer/src/Exception.php';
require __DIR__ . '/../config/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../config/PHPMailer/src/SMTP.php';

function enviarCorreoNotificacion($correoDestino, $nombre, $asunto, $mensajeHTML) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'proyectoprograweb2911@gmail.com';
        $mail->Password = 'ywtlgxnercgpmgyq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Información del remitente y destinatario
        $mail->setFrom('proyectoprograweb2911@gmail.com', 'Aventones');
        $mail->addAddress($correoDestino, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
