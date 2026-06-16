<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$docente_id      = $_POST["docente_id"] ?? null;
$docente_nombre  = $_POST["docente_nombre"] ?? null;
$docente_correo  = $_POST["docente_correo"] ?? null;
$docente_asignatura = $_POST["docente_asignatura"] ?? null;
$nueva_password  = $_POST["docente_contrasena"] ?? null;

if (!$docente_id) {
    echo json_encode(["success" => false, "message" => "Falta usuario_id"]);
    exit;
}

// Si viene contraseña nueva, actualizarla también
if (!empty($nueva_password)) {
    $hash = password_hash($nueva_password, PASSWORD_BCRYPT);
    $sql = "UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ?, asignatura = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $docente_nombre, $docente_correo, $hash, $docente_asignatura, $docente_id);
} else {
    // Sin cambio de contraseña
    $sql = "UPDATE usuarios SET nombre = ?, correo = ?, asignatura = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $docente_nombre, $docente_correo, $docente_asignatura, $docente_id);
}

$stmt->execute();

echo json_encode(["success" => true, "message" => "Docente actualizado correctamente"]);
