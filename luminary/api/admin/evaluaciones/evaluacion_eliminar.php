<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$evaluacion_id = $_POST["evaluacion_id"] ?? null;

if (!$evaluacion_id) {
    echo json_encode(["success" => false, "message" => "Falta evaluacion_id"]);
    exit;
}

try {
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");

    // Eliminar notas asociadas a la evaluación
    $sqlNotas = "DELETE FROM notas WHERE evaluacion_id = ?";
    $stmtNotas = $conexion->prepare($sqlNotas);
    $stmtNotas->bind_param("i", $evaluacion_id);
    $stmtNotas->execute();

    // Eliminar la evaluación
    $sqlEv = "DELETE FROM evaluaciones WHERE id = ?";
    $stmtEv = $conexion->prepare($sqlEv);
    $stmtEv->bind_param("i", $evaluacion_id);
    $stmtEv->execute();

    // Reactivar FK checks
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");

    echo json_encode([
        "success" => true,
        "message" => "Evaluación eliminada correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al eliminar evaluación"]);
}