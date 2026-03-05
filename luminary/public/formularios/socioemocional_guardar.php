<?php
session_start();
require_once __DIR__ . "/../../api/config/db.php";

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$config = require '/home2/sanignac/pass.php';

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
$nombre_estudiante        = mb_strtoupper(limpiar($_POST['nombre_estudiante']), 'UTF-8')  ;
$apellidos_estudiante     = mb_strtoupper(limpiar($_POST['apellidos_estudiante']), 'UTF-8');
$rut_estudiante           = limpiar($_POST['rut_estudiante']);
$correo_estudiante        = limpiar($_POST['correo_estudiante']);
$telefono_estudiante      = limpiar($_POST['telefono_estudiante']);
$curso_estudiante         = limpiar($_POST['curso_estudiante']);


// Consulta SQL
$sql = "INSERT INTO apoyo_socioemocional (
            nombre_estudiante,
            apellidos_estudiante,
            rut_estudiante,
            correo_estudiante,
            telefono_estudiante,
            curso_estudiante,
            fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssis",
    $nombre_estudiante,
    $apellidos_estudiante,
    $rut_estudiante,
    $correo_estudiante,
    $telefono_estudiante,
    $curso_estudiante
);

if ($stmt->execute()) {

    // Obtener etiqueta legible del curso preferido (nivel + letra) si viene como id
    $curso_mostrar = $curso_estudiante;
    if (!empty($curso_estudiante) && is_numeric($curso_estudiante)) {
        $q = $conexion->prepare("SELECT nivel, letra FROM cursos WHERE id = ? LIMIT 1");
        $q->bind_param("i", $curso_estudiante);
        $q->execute();
        $res = $q->get_result();
        if ($row = $res->fetch_assoc()) {
            $curso_mostrar = $row['nivel'] . $row['letra'];
        }
        $q->close();
    }
    $mail = new PHPMailer(true);

    try {
        // CONFIG SMTP
        $mail->isSMTP();
        $mail->Host       = 'mail.sanignaciova.cl';
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['SOCIOEMOCIONAL_USER'];
        $mail->Password   = $config['SOCIOEMOCIONAL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // REMITENTE
        $mail->setFrom('apoyosocioemocional@sanignaciova.cl', 'Formulario Apoyo Socioemocional');

        // DESTINATARIO
        $mail->addAddress('maturana.or.adrian@gmail.com'); // ← CORREO QUE RECIBE EL AVISO
        $mail->addBCC('centroestudiossanignacio@vtr.net');
        $mail->addBCC('francisco.p.gatica@gmail.com');

        // CONTENIDO
        $mail->isHTML(true);
        $mail->Subject = "Estudiante apoyo socioemocional registrado: $nombre_estudiante $apellidos_estudiante";

        $mail->Body = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .container {
                    margin: 0 auto;
                    background-color: #035bad;
                    padding: 20px;
                }
                .content {
                    background-color: #ffffff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
                h2 {
                    color: #035bad;
                    border-bottom: 2px solid #035bad;
                    padding-bottom: 10px;
                }
                h3 {
                    color: #035bad;
                    margin-top: 25px;
                }
                .info-block {
                    margin-bottom: 15px;
                }
                strong {
                    color: #333;
                    display: inline-block;
                    width: 200px;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='content'>
                    <h2>Nueva Ficha de Estudiante Apoyo Socioemocional</h2>
                    
                    <div class='info-section'>
                        <h3>Datos del Estudiante</h3>
                        <div class='info-block'><strong>Estudiante:</strong> $nombre_estudiante $apellidos_estudiante</div>
                        <div class='info-block'><strong>RUT:</strong> $rut_estudiante</div>
                        <div class='info-block'><strong>Correo:</strong> $correo_estudiante</div>
                        <div class='info-block'><strong>Teléfono:</strong> $telefono_estudiante</div>
                        <div class='info-block'><strong>Curso preferido:</strong> $curso_mostrar</div>
                    </div>
                    
                    <div class='footer'>
                        <p>Este es un correo automático del sistema de apoyo socioemocional.</p>
                        <p>Fecha de registro: " . date('d/m/Y H:i:s') . "</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
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

