<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_estudiante = $_POST["id_estudiante"] ?? null;

if (!$id_estudiante) {
    echo json_encode(["success" => false, "message" => "Falta id_estudiante"]);
    exit;
}

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // 1️⃣ Eliminar matriculas primero (tabla padre)
    $sqlMatricula = "DELETE FROM matriculas WHERE estudiante_id = ?";
    $stmtMatricula = $conexion->prepare($sqlMatricula);
    $stmtMatricula->bind_param("i", $id_estudiante);
    $stmtMatricula->execute();
    $stmtMatricula->close();

    // 2️⃣ Eliminar notas
    $sqlNotas = "DELETE FROM notas WHERE estudiante_id = ?";
    $stmtNotas = $conexion->prepare($sqlNotas); // ← Corregido: era $sqlEstudiante
    $stmtNotas->bind_param("i", $id_estudiante);
    $stmtNotas->execute();
    $stmtNotas->close();

    // 3️⃣ Eliminar estudiante al final
    $sqlEstudiante = "DELETE FROM estudiantes WHERE id = ?";
    $stmtEstudiante = $conexion->prepare($sqlEstudiante);
    $stmtEstudiante->bind_param("i", $id_estudiante);
    $stmtEstudiante->execute();
    $stmtEstudiante->close();

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Estudiante eliminado correctamente"
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar estudiante"]);
}