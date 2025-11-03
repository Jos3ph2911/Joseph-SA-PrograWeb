<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga PHPMailer desde la ruta correcta (usa __DIR__ para rutas absolutas)
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function enviarCorreoActivacion($correoDestino, $nombre, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'proyectoprograweb2911@gmail.com'; // tu correo Gmail
        $mail->Password = 'ywtlgxnercgpmgyq';                // tu App Password (16 caracteres)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Información del remitente y destinatario
        $mail->setFrom('proyectoprograweb2911@gmail.com', 'Aventones');
        $mail->addAddress($correoDestino, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Activación de cuenta - Aventones';

        $enlace = "http://isw.utn.ac.cr/Proyecto1/activar.php?token=$token";
        $mail->Body = "
            <h2>Hola $nombre,</h2>
            <p>Gracias por registrarte en <strong>Aventones</strong>.</p>
            <p>Para activar tu cuenta, haz clic en el siguiente enlace:</p>
            <a href='$enlace'>$enlace</a>
        ";

        // (opcional) Depuración — muestra detalles de conexión
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Error enviando correo: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
