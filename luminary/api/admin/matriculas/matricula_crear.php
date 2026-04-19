<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

// Validar sesión
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

// Obtener datos enviados
$nombre_estudiante = $_POST["nombre_estudiante"] ?? null;
$apellidos_estudiante = $_POST["apellidos_estudiante"] ?? null;
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? null;
$rut_estudiante = $_POST["rut_estudiante"] ?? null;
$serie_carnet_estudiante = $_POST["serie_carnet_estudiante"] ?? null;
$etnia_estudiante = $_POST["etnia_estudiante"] ?? null;
$direccion_estudiante = $_POST["direccion_estudiante"] ?? null;
$correo_estudiante = $_POST["correo_estudiante"] ?? null;
$curso_preferido = $_POST["curso_preferido"] ?? null;
$telefono_estudiante = $_POST["telefono_estudiante"] ?? null;
$hijos_estudiante = $_POST["hijos_estudiante"] ?? null;
$situacion_especial_estudiante = $_POST["situacion_especial_estudiante"] ?? null;
$programa_estudiante = $_POST["programa_estudiante"] ?? null;
$nombre_apoderado = $_POST["nombre_apoderado"] ?? null;
$rut_apoderado = $_POST["rut_apoderado"] ?? null;
$parentezco_apoderado = $_POST["parentezco_apoderado"] ?? null;
$direccion_apoderado = $_POST["direccion_apoderado"] ?? null;
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
    $sql = "INSERT INTO matriculas 
            (nombre_estudiante, apellidos_estudiante, fecha_nacimiento, rut_estudiante, serie_carnet_estudiante, etnia_estudiante, direccion_estudiante, correo_estudiante, curso_preferido, telefono_estudiante, hijos_estudiante, situacion_especial_estudiante, programa_estudiante, nombre_apoderado, rut_apoderado, parentezco_apoderado, direccion_apoderado, telefono_apoderado, situacion_especial_apoderado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssissssssssss", $nombre_estudiante, $apellidos_estudiante, $fecha_nacimiento, $rut_estudiante, $serie_carnet_estudiante, $etnia_estudiante, $direccion_estudiante, $correo_estudiante, $curso_preferido, $telefono_estudiante, $hijos_estudiante, $situacion_especial_estudiante, $programa_estudiante, $nombre_apoderado, $rut_apoderado, $parentezco_apoderado, $direccion_apoderado, $telefono_apoderado, $situacion_especial_apoderado);
    $stmt->execute();

    $matricula_id = $stmt->insert_id;

    // Insertar estudiante
    $insertEst = $conexion->prepare("INSERT INTO estudiantes (matricula_id, curso_id) VALUES (?, ?)");
    $insertEst->bind_param("ii", $matricula_id, $curso_preferido);
    $insertEst->execute();

    $estudiante_id = $conexion->insert_id;

    // Actualizar matrícula con estudiante_id y estado
    $updateMat = $conexion->prepare("UPDATE matriculas SET estudiante_id = ?, estado = 'Activa' WHERE id = ?");
    $updateMat->bind_param("ii", $estudiante_id, $matricula_id);
    $updateMat->execute();

    echo json_encode([
        "success" => true,
        "matricula_id" => $matricula_id,
        "estudiante_id" => $estudiante_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al guardar matrícula"
    ]);
}
