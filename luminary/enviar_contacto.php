<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$config = require '/home2/sanignac/pass.php';

// ============================
// 1. VALIDACIÓN ANTI-SPAM (honeypot + tiempo)
// ============================

// Honeypot: si este campo viene lleno, es un bot
if (!empty($_POST['website'])) {
    // Respuesta "silenciosa" para no alertar al bot
    header("Location: /index.html?exito=1");
    exit();
}

// Verificación de tiempo mínimo de carga del formulario
$tiempoMinimoSegundos = 3;

if (empty($_POST['form_timestamp'])) {
    exit("Solicitud inválida.");
}

$tiempoEnviado = (int) $_POST['form_timestamp']; // milisegundos
$tiempoActual = round(microtime(true) * 1000);
$tiempoTranscurrido = ($tiempoActual - $tiempoEnviado) / 1000;

if ($tiempoTranscurrido < $tiempoMinimoSegundos) {
    // Envío demasiado rápido, probablemente un bot
    header("Location: /index.html?exito=1");
    exit();
}

// ============================
// 2. Recibir datos
// ============================
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$nombre || !$email || !$mensaje) {
    exit("Faltan datos en el formulario.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("El email ingresado no es válido.");
}

// Sanitizar para evitar inyección de headers
$nombre = str_replace(["\r", "\n"], '', $nombre);
$email  = str_replace(["\r", "\n"], '', $email);

// Escapar HTML para evitar que alguien inyecte código en el correo
$nombreSafe = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$emailSafe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$mensajeSafe = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

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
        <div style='width: 100%; background-color: #035bad; font-family: Outfit, sans-serif; padding: 1rem 0;'>
            <div style='margin: 1rem auto; max-width:400px; background-color: #eee; padding: 2rem 2rem; box-shadow: 0 0 1rem rgba(0, 0, 0, 50%);'>
                <h2>Nuevo mensaje recibido</h2>
                <p><strong>Nombre:</strong> $nombreSafe</p>
                <p><strong>Email:</strong> $emailSafe</p>
                <p><strong>Mensaje:</strong><br>$mensajeSafe</p>
            </div>
        </div>
    ";

    $mail->send();

    header("Location: /index.html?exito=1");
    exit();

} catch (Exception $e) {
    echo "Error al enviar mensaje: {$mail->ErrorInfo}";
}