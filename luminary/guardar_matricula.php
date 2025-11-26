<?php
session_start();
require_once 'conexion.php'; // Ajusta la ruta si es necesario

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar que los datos vienen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Método no permitido.");
}

// Sanitizar datos
function limpiar($campo) {
    return htmlspecialchars(trim($campo));
}

// Datos del estudiante
$nombre_estudiante        = limpiar($_POST['nombre_estudiante']);
$apellidos_estudiante     = limpiar($_POST['apellidos_estudiante']);
$fecha_nacimiento         = limpiar($_POST['fecha_nacimiento']);
$rut_estudiante           = limpiar($_POST['rut_estudiante']);
$serie_carnet_estudiante  = limpiar($_POST['serie_carnet_estudiante']);
$direccion_estudiante     = limpiar($_POST['direccion_estudiante']);
$correo_estudiante        = limpiar($_POST['correo_estudiante']);
$telefono_estudiante      = limpiar($_POST['telefono_estudiante']);
$curso_preferido          = limpiar($_POST['curso_preferido']);
$jornada_preferida        = limpiar($_POST['jornada_preferida']);

// Datos del apoderado
$nombre_apoderado         = limpiar($_POST['nombre_apoderado']);
$rut_apoderado            = limpiar($_POST['rut_apoderado']);
$direccion_apoderado      = limpiar($_POST['direccion_apoderado']);

// Consulta SQL
$sql = "INSERT INTO matriculas (
            nombre_estudiante,
            apellidos_estudiante,
            fecha_nacimiento,
            rut_estudiante,
            serie_carnet_estudiante,
            direccion_estudiante,
            correo_estudiante,
            telefono_estudiante,
            curso_preferido,
            jornada_preferida,
            nombre_apoderado,
            rut_apoderado,
            direccion_apoderado,
            fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssssssssss",
    $nombre_estudiante,
    $apellidos_estudiante,
    $fecha_nacimiento,
    $rut_estudiante,
    $serie_carnet_estudiante,
    $direccion_estudiante,
    $correo_estudiante,
    $telefono_estudiante,
    $curso_preferido,
    $jornada_preferida,
    $nombre_apoderado,
    $rut_apoderado,
    $direccion_apoderado
);

if ($stmt->execute()) {
    $mail = new PHPMailer(true);

    try {
        // CONFIG SMTP
        $mail->isSMTP();
        $mail->Host       = 'mail.sanignaciova.cl';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admision@sanignaciova.cl';   // ← CAMBIAR
        $mail->Password   = 'admisionsanignaciova';          // ← CAMBIAR (contraseña de aplicación)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // REMITENTE
        $mail->setFrom('admision@sanignaciova.cl', 'Sistema de Matrículas');

        // DESTINATARIO
        $mail->addAddress('maturana.or.adrian@gmail.com'); // ← CORREO QUE RECIBE EL AVISO
        $mail->addBCC('centroestudiossanignacio@vtr.net');
        $mail->addBCC('francisco.p.gatica@gmail.com');

        // CONTENIDO
        $mail->isHTML(true);
        $mail->Subject = "Nueva matrícula registrada: $nombre_estudiante $apellidos_estudiante";

        $mail->Body = "
            <div style='width: 100%; background-color: #035bad; font-family: Outfit, sans-serif; padding-bottom: 1rem;'>
                <div style='margin:0 auto; max-width:400px; display: flex; justify-content: center; padding: 1rem 0 0 0 '>
                    <img height='75px' src='https://sanignaciova.cl/assets/icons/logo-2.svg'>
                </div>
                <div style='margin: 1rem auto; max-width:400px; background-color: #eee; padding: 2rem 2rem; box-shadow: 0 0 1rem rgba(0, 0, 0, 50%);'>
                    <h2>Nueva Ficha de Matrícula</h2>
                    <h3>Datos del Estudiante</h3>
                    <p><strong>Estudiante:</strong> $nombre_estudiante $apellidos_estudiante</p>
                    <p><strong>RUT:</strong> $rut_estudiante</p>
                    <p><strong>Fecha nacimiento:</strong> $fecha_nacimiento</p>
                    <p><strong>Dirección:</strong> $direccion_estudiante</p>
                    <p><strong>Correo:</strong> $correo_estudiante</p>
                    <p><strong>Teléfono:</strong> $telefono_estudiante</p>
                    <p><strong>Curso preferido:</strong> $curso_preferido</p>
                    <p><strong>Jornada preferida:</strong> $jornada_preferida</p>
                    <h3>Datos del Apoderado:</h3>
                    <p><strong>Nombre:</strong> $nombre_apoderado</p>
                    <p><strong>RUT:</strong> $rut_apoderado</p>
                    <p><strong>Dirección:</strong> $direccion_apoderado</p>
                </div>
            </div>
        ";

        $mail->send();

    } catch (Exception $e) {
        error_log("Error al enviar correo de matrícula: {$mail->ErrorInfo}");
    }

    // 🔵 Redirigir con éxito
    header("Location: /pages/admision.html?exito=1");
    exit();
} else {
    // 🔴 Mostrar error
    echo "Error al guardar matrícula: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
