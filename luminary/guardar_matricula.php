<?php
session_start();
require_once 'conexion.php'; // Ajusta la ruta si es necesario

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
$fecha_nacimiento         = limpiar($_POST['fecha_nacimiento']);
$rut_estudiante           = limpiar($_POST['rut_estudiante']);
$etnia_estudiante         = limpiar($_POST['etnia_estudiante']);
$serie_carnet_estudiante  = limpiar($_POST['serie_carnet_estudiante']);
$situacion_especial_estudiante  = mb_strtoupper(limpiar($_POST['situacion_especial_estudiante']), 'UTF-8'); 
$direccion_estudiante     = mb_strtoupper(limpiar($_POST['direccion_estudiante']), 'UTF-8'); 
$correo_estudiante        = limpiar($_POST['correo_estudiante']);
$telefono_estudiante      = limpiar($_POST['telefono_estudiante']);
$curso_preferido          = limpiar($_POST['curso_preferido']);
$jornada_preferida        = "";

// Datos del apoderado
$nombre_apoderado         = mb_strtoupper(limpiar($_POST['nombre_apoderado']), 'UTF-8');
$rut_apoderado            = limpiar($_POST['rut_apoderado']);
$direccion_apoderado      = mb_strtoupper(limpiar($_POST['direccion_apoderado']), 'UTF-8'); 
$telefono_apoderado       = limpiar($_POST['telefono_apoderado']);

// Función para asignar jornada por curso
function asignarJornadaPorCurso($curso) {
    // Definir qué cursos van en cada jornada
    $jornadas = [
        'Mañana' => [1, 4, 5],
        'Tarde' => [2, 6, 7],
        'Noche' => [3, 8, 9]
    ];
    
    // Buscar el curso en cada jornada
    foreach ($jornadas as $jornada => $cursos) {
        if (in_array($curso, $cursos)) {
            return $jornada;
        }
    }
    
    return 'Sin información'; // Jornada por defecto si no se encuentra
}

// Determinar jornada preferida
if (isset($_POST['jornada_preferida']) && !empty($_POST['jornada_preferida'])) {
    $jornada_preferida = limpiar($_POST['jornada_preferida']);
} else {
    // Si no, asignar según el curso
    $jornada_preferida = asignarJornadaPorCurso($curso_preferido);
}

// Consulta SQL
$sql = "INSERT INTO matriculas (
            nombre_estudiante,
            apellidos_estudiante,
            fecha_nacimiento,
            rut_estudiante,
            serie_carnet_estudiante,
            etnia_estudiante,
            direccion_estudiante,
            correo_estudiante,
            telefono_estudiante,
            situacion_especial_estudiante,
            curso_preferido,
            jornada_preferida,
            nombre_apoderado,
            rut_apoderado,
            direccion_apoderado,
            telefono_apoderado,
            fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssssssssssssssss",
    $nombre_estudiante,
    $apellidos_estudiante,
    $fecha_nacimiento,
    $rut_estudiante,
    $serie_carnet_estudiante,
    $etnia_estudiante,
    $direccion_estudiante,
    $correo_estudiante,
    $telefono_estudiante,
    $situacion_especial_estudiante,
    $curso_preferido,
    $jornada_preferida,
    $nombre_apoderado,
    $rut_apoderado,
    $direccion_apoderado,
    $telefono_apoderado
);

if ($stmt->execute()) {
    $mail = new PHPMailer(true);

    try {
        // CONFIG SMTP
        $mail->isSMTP();
        $mail->Host       = 'mail.sanignaciova.cl';
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['ADMISION_USER'];
        $mail->Password   = $config['ADMISION_PASS'];
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
        <!DOCTYPE html>
        <html>
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
                    max-width: 600px;
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
                    <h2>Nueva Ficha de Matrícula</h2>
                    
                    <div class='info-section'>
                        <h3>📋 Datos del Estudiante</h3>
                        <div class='info-block'><strong>Estudiante:</strong> $nombre_estudiante $apellidos_estudiante</div>
                        <div class='info-block'><strong>RUT:</strong> $rut_estudiante</div>
                        <div class='info-block'><strong>N° serie carnet:</strong> $serie_carnet_estudiante</div>
                        <div class='info-block'><strong>Etnia:</strong> $etnia_estudiante</div>
                        <div class='info-block'><strong>Situación especial:</strong> $situacion_especial_estudiante</div>
                        <div class='info-block'><strong>Fecha nacimiento:</strong> $fecha_nacimiento</div>
                        <div class='info-block'><strong>Dirección:</strong> $direccion_estudiante</div>
                        <div class='info-block'><strong>Correo:</strong> $correo_estudiante</div>
                        <div class='info-block'><strong>Teléfono:</strong> $telefono_estudiante</div>
                        <div class='info-block'><strong>Curso preferido:</strong> $curso_preferido</div>
                        <div class='info-block'><strong>Jornada preferida:</strong> $jornada_preferida</div>
                    </div>
                    
                    <div class='info-section'>
                        <h3>👨‍👦 Datos del Apoderado</h3>
                        <div class='info-block'><strong>Nombre:</strong> $nombre_apoderado</div>
                        <div class='info-block'><strong>RUT:</strong> $rut_apoderado</div>
                        <div class='info-block'><strong>Dirección:</strong> $direccion_apoderado</div>
                        <div class='info-block'><strong>Teléfono:</strong> $telefono_apoderado</div>
                    </div>
                    
                    <div class='footer'>
                        <p>Este es un correo automático del sistema de matrículas.</p>
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
?>
