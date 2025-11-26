<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$config = require '/home2/sanignac/pass.php';

// Recibir datos
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if (!$nombre || !$email || !$mensaje) {
    exit("Faltan datos en el formulario.");
}

$mail = new PHPMailer(true);

try {
    // CONFIG SMTP (Servidor del hosting)
    $mail->isSMTP();
    $mail->Host       = 'mail.sanignaciova.cl'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['CONTACTO_USER']; 
    $mail->Password   = $config['CONTACTO_PASS']; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // REMITENTE (tu correo institucional)
    $mail->setFrom('contacto@sanignaciova.cl', 'Formulario de Contacto');

    // DESTINATARIO (tú)
    $mail->addAddress('maturana.or.adrian@gmail.com');
    $mail->addBCC('centroestudiossanignacio@vtr.net');
    $mail->addBCC('francisco.p.gatica@gmail.com');

    // OPCIONAL: mandar copia al usuario
    $mail->addReplyTo($email, $nombre);
    // $mail->addCC($email);

    // CONTENIDO
    $mail->isHTML(true);
    $mail->Subject = "Nuevo mensaje desde el formulario de contacto";

    $mail->Body = "
        <div style='width: 100%; background-color: #035bad; font-family: Outfit, sans-serif; padding-bottom: 1rem;'>
            <div style='margin: 1rem auto; max-width:400px; background-color: #eee; padding: 2rem 2rem; box-shadow: 0 0 1rem rgba(0, 0, 0, 50%);'>
                <h2>Nuevo mensaje recibido</h2>
                <p><strong>Nombre:</strong> $nombre</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Mensaje:</strong><br>$mensaje</p>
            </div>
        </div>
    ";

    $mail->send();

    header("Location: /index.html?exito=1");
    exit();

} catch (Exception $e) {
    echo "Error al enviar mensaje: {$mail->ErrorInfo}";
}
