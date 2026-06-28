<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

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
            situacion_especial_apoderado = ?
        WHERE rut_estudiante = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssssssssssssssssssss",
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
    $rut_estudiante  // WHERE condition
);

$stmt->execute();

echo json_encode(["success" => true, "message" => "Matricula actualizada correctamente"]);