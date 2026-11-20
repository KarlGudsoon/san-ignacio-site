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

$tablasPermitidas = ["matriculas_formulario", "matriculas_proximo_anio"];
$tabla = $_POST["tabla"] ?? "matriculas_formulario";

if (!in_array($tabla, $tablasPermitidas, true)) {
    echo json_encode(["success" => false, "message" => "Tabla no válida"]);
    exit;
}

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // 1️⃣ Eliminar matriculas primero (tabla padre)
    $sqlMatricula = "DELETE FROM $tabla WHERE id = ?";
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