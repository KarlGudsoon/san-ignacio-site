<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_matricula = $_POST["id_matricula"] ?? null;

if (!$id_matricula) {
    echo json_encode(["success" => false, "message" => "Falta id_matricula"]);
    exit;
}

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // 1️⃣ Eliminar matriculas primero (tabla padre)
    $sqlMatricula = "DELETE FROM matriculas_formulario WHERE id = ?";
    $stmtMatricula = $conexion->prepare($sqlMatricula);
    $stmtMatricula->bind_param("i", $id_matricula);
    $stmtMatricula->execute();
    $stmtMatricula->close();


    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Solicitud de matrícula eliminada correctamente"
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar matrícula"]);
}