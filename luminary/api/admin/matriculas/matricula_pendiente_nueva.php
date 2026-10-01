<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}


require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';
require '../../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar que los datos vienen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Método no permitido.");
}

// Obtener datos enviados
$matricula_ano = $_POST["matricula_ano"] ?? null;
$nombre_estudiante = strtoupper($_POST["nombre_estudiante"] ?? null);
$apellidos_estudiante = strtoupper($_POST["apellidos_estudiante"] ?? null);
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? null;
$rut_estudiante = $_POST["rut_estudiante"] ?? null;
$serie_carnet_estudiante = $_POST["serie_carnet_estudiante"] ?? null;
$etnia_estudiante = $_POST["etnia_estudiante"] ?? null;
$direccion_estudiante = strtoupper($_POST["direccion_estudiante"] ?? null);
$correo_estudiante = $_POST["correo_estudiante"] ?? null;
$curso_preferido = $_POST["curso_preferido"] ?? null;
$telefono_estudiante = $_POST["telefono_estudiante"] ?? null;
$hijos_estudiante = $_POST["hijos_estudiante"] ?? null;
$situacion_especial_estudiante = $_POST["situacion_especial_estudiante"] ?? null;
$programa_estudiante = strtoupper($_POST["programa_estudiante"] ?? null);
$nombre_apoderado = strtoupper($_POST["nombre_apoderado"] ?? null);
$rut_apoderado = $_POST["rut_apoderado"] ?? null;
$parentezco_apoderado = $_POST["parentezco_apoderado"] ?? null;
$direccion_apoderado = strtoupper($_POST["direccion_apoderado"] ?? null);
$telefono_apoderado = $_POST["telefono_apoderado"] ?? null;
$situacion_especial_apoderado = $_POST["situacion_especial_apoderado"] ?? null;

// Validación básica
if (!$nombre_estudiante || !$apellidos_estudiante || !$rut_estudiante || !$curso_preferido) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan datos obligatorios"
    ]);
    exit;
}

try {
    // ✅ Insertar matrícula
    $sql = "INSERT INTO matriculas_formulario 
            (matricula_ano, nombre_estudiante, apellidos_estudiante, fecha_nacimiento, rut_estudiante, serie_carnet_estudiante, etnia_estudiante, direccion_estudiante, correo_estudiante, curso_preferido, telefono_estudiante, hijos_estudiante, situacion_especial_estudiante, programa_estudiante, nombre_apoderado, rut_apoderado, parentezco_apoderado, direccion_apoderado, telefono_apoderado, situacion_especial_apoderado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issssssssissssssssss", $matricula_ano, $nombre_estudiante, $apellidos_estudiante, $fecha_nacimiento, $rut_estudiante, $serie_carnet_estudiante, $etnia_estudiante, $direccion_estudiante, $correo_estudiante, $curso_preferido, $telefono_estudiante, $hijos_estudiante, $situacion_especial_estudiante, $programa_estudiante, $nombre_apoderado, $rut_apoderado, $parentezco_apoderado, $direccion_apoderado, $telefono_apoderado, $situacion_especial_apoderado);
    $stmt->execute();

    $matricula_pendiente_id = $stmt->insert_id;

    

    echo json_encode([
        "success" => true,
        "matricula_pendiente_id" => $matricula_pendiente_id,
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al guardar matrícula"
    ]);
}


// if ($stmt->execute()) {

//     // Obtener etiqueta legible del curso preferido (nivel + letra) si viene como id
//     $curso_mostrar = $curso_preferido;
//     if (!empty($curso_preferido) && is_numeric($curso_preferido)) {
//         $q = $conexion->prepare("SELECT nivel, letra FROM cursos WHERE id = ? LIMIT 1");
//         $q->bind_param("i", $curso_preferido);
//         $q->execute();
//         $res = $q->get_result();
//         if ($row = $res->fetch_assoc()) {
//             $curso_mostrar = $row['nivel'] . $row['letra'];
//         }
//         $q->close();
//     }
//     $mail = new PHPMailer(true);

    

//     try {
//         // CONFIG SMTP
//         $mail->isSMTP();
//         $mail->Host       = 'mail.sanignaciova.cl';
//         $mail->SMTPAuth   = true;
//         $mail->Username   = $config['ADMISION_USER'];
//         $mail->Password   = $config['ADMISION_PASS'];
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//         $mail->Port       = 465;
//         $mail->CharSet = 'UTF-8';
//         $mail->Encoding = 'base64';

//         $mail->addEmbeddedImage(
//             __DIR__ . '/../../../../assets/img/logo.svg', // ruta física del archivo
//             'logo',                                    // CID (identificador único)
//             'logo.svg'                                 // nombre del archivo
//         );

//         // REMITENTE
//         $mail->setFrom('admision@sanignaciova.cl', 'Sistema de Matrículas');

//         // DESTINATARIO
//         $mail->addAddress('francisco.p.gatica@gmail.com'); // ← CORREO QUE RECIBE EL AVISO 
//         $mail->addBCC('centroestudiossanignacio@vtr.net');
//         $mail->addBCC('maturana.or.adrian@gmail.com');

//         // CONTENIDO
//         $mail->isHTML(true);
//         $mail->Subject = "Nueva matrícula registrada: $nombre_estudiante $apellidos_estudiante";

//         $mail->Body = "
//         <!DOCTYPE html>
//         <html lang='es'>
//         <head>
//             <meta charset='UTF-8'>
//             <style>
//                 body {
//                     font-family: Arial, sans-serif;
//                     margin: 0;
//                     padding: 20px;
//                     background-color: #f4f4f4;
//                 }
//                 .container {
//                     margin: 0 auto;
//                     background-color: #035bad;
//                     padding: 20px;
//                 }
//                 .content {
//                     background-color: #ffffff;
//                     padding: 30px;
//                     border-radius: 10px;
//                     box-shadow: 0 4px 8px rgba(0,0,0,0.1);
//                 }
//                 h2 {
//                     color: #035bad;
//                     border-bottom: 2px solid #035bad;
//                     padding-bottom: 10px;
//                 }
//                 h3 {
//                     color: #035bad;
//                     margin-top: 25px;
//                 }
//                 .info-block {
//                     margin-bottom: 15px;
//                 }
//                 strong {
//                     color: #333;
//                     display: inline-block;
//                     width: 200px;
//                 }
//                 .footer {
//                     margin-top: 30px;
//                     padding-top: 20px;
//                     border-top: 1px solid #ddd;
//                     color: #666;
//                     font-size: 12px;
//                 }
//             </style>
//         </head>
//         <body>
//             <div class='container'>
//                 <div class='content'>
//                     <img src='cid:logo' alt='Colegio San Ignacio' style='max-width:50px; margin-bottom:20px;'>
//                     <h2>Nueva Ficha de Matrícula</h2>
//                     <div class='info-section'>
//                         <h3>Datos del Estudiante</h3>
//                         <div class='info-block'><strong>Estudiante:</strong> $nombre_estudiante $apellidos_estudiante</div>
//                         <div class='info-block'><strong>RUT:</strong> $rut_estudiante</div>
//                         <div class='info-block'><strong>N° serie carnet:</strong> $serie_carnet_estudiante</div>
//                         <div class='info-block'><strong>Etnia:</strong> $etnia_estudiante</div>
//                         <div class='info-block'><strong>Situación especial:</strong> $situacion_especial_estudiante</div>
//                         <div class='info-block'><strong>Fecha nacimiento:</strong> $fecha_nacimiento</div>
//                         <div class='info-block'><strong>Dirección:</strong> $direccion_estudiante</div>
//                         <div class='info-block'><strong>Correo:</strong> $correo_estudiante</div>
//                         <div class='info-block'><strong>Teléfono:</strong> $telefono_estudiante</div>
//                         <div class='info-block'><strong>Curso preferido:</strong> $curso_mostrar</div>
//                         <div class='info-block'><strong>Jornada preferida:</strong> $jornada_preferida</div>
//                     </div>
                    
//                     <div class='info-section'>
//                         <h3>Datos del Apoderado</h3>
//                         <div class='info-block'><strong>Nombre:</strong> $nombre_apoderado</div>
//                         <div class='info-block'><strong>RUT:</strong> $rut_apoderado</div>
//                         <div class='info-block'><strong>Dirección:</strong> $direccion_apoderado</div>
//                         <div class='info-block'><strong>Teléfono:</strong> $telefono_apoderado</div>
//                     </div>
                    
//                     <div class='footer'>
//                         <p>Este es un correo automático del sistema de matrículas.</p>
//                         <p>Fecha de registro: " . date('d/m/Y H:i:s') . "</p>
//                     </div>
//                 </div>
//             </div>
//         </body>
//         </html>
//         ";

//         $mail->send();

//     } catch (Exception $e) {
//         error_log("Error al enviar correo de matrícula: {$mail->ErrorInfo}");
//     }

//     if (!empty($correo_estudiante)) {
//         $mailConfirm = new PHPMailer(true);
//         try {
//             // CONFIG SMTP
//             $mailConfirm->isSMTP();
//             $mailConfirm->Host       = 'mail.sanignaciova.cl';
//             $mailConfirm->SMTPAuth   = true;
//             $mailConfirm->Username   = $config['ADMISION_USER'];
//             $mailConfirm->Password   = $config['ADMISION_PASS'];
//             $mailConfirm->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//             $mailConfirm->Port       = 465;
//             $mailConfirm->CharSet = 'UTF-8';
//             $mailConfirm->Encoding = 'base64';

//             $mailConfirm->addEmbeddedImage(
//                 __DIR__ . '/../../../../assets/icon/icon-park-solid--success.svg', // ruta física del archivo
//                 'confirmacion',                                    // CID (identificador único)
//                 'icon-park-solid--success.svg'                                 // nombre del archivo
//             );

//             // REMITENTE
//             $mailConfirm->setFrom('admision@sanignaciova.cl', 'Centro de Estudios San Ignacio de Villa Alemana');

//             // DESTINATARIO
//             $mailConfirm->addAddress($correo_estudiante);

//             // CONTENIDO
//             $mailConfirm->isHTML(true);
//             $mailConfirm->Subject = "Confirmación de recepción de matrícula";

//             $mailConfirm->Body = "
//             <!DOCTYPE html>
//             <html lang='es'>
//             <head><meta charset='UTF-8'></head>
//             <body style='font-family: Arial, sans-serif; padding:20px; background:#f4f4f4;'>
//                 <div style='max-width:600px; color: #fff; margin:0 auto; text-align:center; background:#03ad77; padding:30px; border-radius:10px;'>
//                     <img src='cid:confirmacion' alt='Confirmación' style='max-width:100px; margin-bottom:20px;'>
//                     <h2 style='color:#fff;'>Formulario realizado con éxito</h2>
//                     <p>Estimado/a <strong>$nombre_estudiante $apellidos_estudiante</strong>,</p>
//                     <p>Hemos recibido correctamente los datos de tu postulación para el curso <strong>$curso_mostrar</strong>, jornada <strong>$jornada_preferida</strong>.</p>
//                     <p>Nos pondremos en contacto contigo para revisar su solicitud.</p>
//                     <p>Dudas y consultas al Whatsapp<a style='margin-left: 10px; color: white;' href='https://wa.me/+56996116669'>+56996116669</a></p>
//                     <p style='margin-top:30px; font-size:12px; color:#01010173;'>Este es un correo automático, por favor no responder directamente a este mensaje.</p>
//                 </div>
//             </body>
//             </html>
//             ";

//             $mailConfirm->send();

//         } catch (Exception $e) {
//             error_log("Error al enviar correo de confirmación: {$mailConfirm->ErrorInfo}");
//         }
//     }

//     // 🔵 Redirigir con éxito
//     header("Location: /pages/admision.html?exito=1");
//     exit();
// } else {
//     // 🔴 Mostrar error
//     echo "Error al guardar matrícula: " . $stmt->error;
// }

?>
