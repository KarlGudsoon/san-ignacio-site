<?php
session_start();
require_once 'conexion.php'; // Ajusta la ruta si es necesario

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
