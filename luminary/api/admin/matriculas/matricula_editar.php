<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_matricula = (int)($_POST["id_matricula"] ?? 0);
$matricula_ano = (int)($_POST["matricula_ano"] ?? 0);
$nombre_estudiante = strtoupper($_POST["nombre_estudiante"] ?? "");
$apellidos_estudiante = strtoupper($_POST["apellidos_estudiante"] ?? "");
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
$rut_estudiante = $_POST["rut_estudiante"] ?? "";
$serie_carnet_estudiante = $_POST["serie_carnet_estudiante"] ?? "";
$etnia_estudiante = $_POST["etnia_estudiante"] ?? "";
$direccion_estudiante = strtoupper($_POST["direccion_estudiante"] ?? "");
$correo_estudiante = $_POST["correo_estudiante"] ?? "";
$curso_preferido = (int)($_POST["curso_preferido"] ?? 0);
$telefono_estudiante = $_POST["telefono_estudiante"] ?? "";
$hijos_estudiante = (int)($_POST["hijos_estudiante"] ?? 0);
$situacion_especial_estudiante = $_POST["situacion_especial_estudiante"] ?? "";
$programa_estudiante = strtoupper($_POST["programa_estudiante"] ?? "");
$nombre_apoderado = strtoupper($_POST["nombre_apoderado"] ?? "");
$rut_apoderado = $_POST["rut_apoderado"] ?? "";
$parentezco_apoderado = $_POST["parentezco_apoderado"] ?? "";
$direccion_apoderado = strtoupper($_POST["direccion_apoderado"] ?? "");
$telefono_apoderado = $_POST["telefono_apoderado"] ?? "";
$situacion_especial_apoderado = $_POST["situacion_especial_apoderado"] ?? "";

if (!$id_matricula || !$nombre_estudiante || !$apellidos_estudiante || !$rut_estudiante || !$curso_preferido) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
    exit;
}

$sql = "UPDATE matriculas_formulario SET 
            nombre_estudiante = ?,
            apellidos_estudiante = ?,
            fecha_nacimiento = ?,
            rut_estudiante = ?,
            serie_carnet_estudiante = ?,
            etnia_estudiante = ?,
            direccion_estudiante = ?,
            correo_estudiante = ?,
            curso_preferido = ?,
            telefono_estudiante = ?,
            hijos_estudiante = ?,
            situacion_especial_estudiante = ?,
            programa_estudiante = ?,
            nombre_apoderado = ?,
            rut_apoderado = ?,
            parentezco_apoderado = ?,
            direccion_apoderado = ?,
            telefono_apoderado = ?,
            situacion_especial_apoderado = ?,
            matricula_ano = ?
        WHERE id = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error al preparar la actualización: " . $conexion->error]);
    exit;
}

$stmt->bind_param(
    "ssssssssisissssssssii",
    $nombre_estudiante,
    $apellidos_estudiante,
    $fecha_nacimiento,
    $rut_estudiante,
    $serie_carnet_estudiante,
    $etnia_estudiante,
    $direccion_estudiante,
    $correo_estudiante,
    $curso_preferido,
    $telefono_estudiante,
    $hijos_estudiante,
    $situacion_especial_estudiante,
    $programa_estudiante,
    $nombre_apoderado,
    $rut_apoderado,
    $parentezco_apoderado,
    $direccion_apoderado,
    $telefono_apoderado,
    $situacion_especial_apoderado,
    $matricula_ano,
    $id_matricula
);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Error al actualizar matrícula: " . $stmt->error]);
    exit;
}

echo json_encode(["success" => true, "message" => "Matricula actualizada correctamente"]);