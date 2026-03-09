<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_profesor = $_SESSION["user_id"];
$evaluacion_id = $_POST["evaluacion_id"] ?? null;

if (!$evaluacion_id) {
    echo json_encode(["success" => false, "message" => "Falta evaluacion_id"]);
    exit;
}

try {
    // 🔒 Verificar que la evaluación pertenece a un curso del profesor logueado
    $sqlValidar = "SELECT e.id 
                   FROM evaluaciones e
                   INNER JOIN curso_profesor cp ON cp.id = e.curso_profesor_id
                   WHERE e.id = ? AND cp.profesor_id = ?";

    $stmtValidar = $conexion->prepare($sqlValidar);
    $stmtValidar->bind_param("ii", $evaluacion_id, $id_profesor);
    $stmtValidar->execute();
    $resultValidar = $stmtValidar->get_result();

    if ($resultValidar->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "No tienes permiso para eliminar esta evaluación"]);
        exit;
    }

    // 🗑️ Eliminar notas asociadas a la evaluación
    $sqlNotas = "DELETE FROM notas WHERE evaluacion_id = ?";
    $stmtNotas = $conexion->prepare($sqlNotas);
    $stmtNotas->bind_param("i", $evaluacion_id);
    $stmtNotas->execute();

    // 🗑️ Eliminar la evaluación
    $sqlEv = "DELETE FROM evaluaciones WHERE id = ?";
    $stmtEv = $conexion->prepare($sqlEv);
    $stmtEv->bind_param("i", $evaluacion_id);
    $stmtEv->execute();

    echo json_encode([
        "success" => true,
        "message" => "Evaluación eliminada correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al eliminar evaluación"]);
}