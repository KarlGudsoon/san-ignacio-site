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

// Obtener datos del POST
$estudiante_id = $_POST["estudiante_id"] ?? null;
$nuevo_curso_id = $_POST["nuevo_curso_id"] ?? null;

if (!$estudiante_id || !$nuevo_curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos: estudiante_id y nuevo_curso_id son requeridos"
    ]);
    exit;
}

// Validar que el estudiante existe
$sqlEstudiante = "SELECT id, curso_id FROM estudiantes WHERE id = ?";
$stmtEstudiante = $conexion->prepare($sqlEstudiante);
$stmtEstudiante->bind_param("i", $estudiante_id);
$stmtEstudiante->execute();
$resultEstudiante = $stmtEstudiante->get_result();

if ($resultEstudiante->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no encontrado"
    ]);
    exit;
}

$estudiante = $resultEstudiante->fetch_assoc();
$curso_actual = $estudiante["curso_id"];

if ($curso_actual == $nuevo_curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "El estudiante ya está en ese curso"
    ]);
    exit;
}

// Validar que el nuevo curso existe
$sqlCurso = "SELECT id FROM cursos WHERE id = ?";
$stmtCurso = $conexion->prepare($sqlCurso);
$stmtCurso->bind_param("i", $nuevo_curso_id);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();

if ($resultCurso->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Curso destino no encontrado"
    ]);
    exit;
}

// Actualizar el curso del estudiante
$sqlUpdate = "UPDATE estudiantes SET curso_id = ? WHERE id = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);
$stmtUpdate->bind_param("ii", $nuevo_curso_id, $estudiante_id);

if ($stmtUpdate->execute()) {
    // Contar y eliminar notas del estudiante
    $sqlCount = "SELECT COUNT(*) as total FROM notas WHERE estudiante_id = ?";
    $stmtCount = $conexion->prepare($sqlCount);
    $stmtCount->bind_param("i", $estudiante_id);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $totalNotas = $resultCount->fetch_assoc()["total"];

    $sqlDelete = "DELETE FROM notas WHERE estudiante_id = ?";
    $stmtDelete = $conexion->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $estudiante_id);
    $stmtDelete->execute();

    echo json_encode([
        "success" => true,
        "message" => "Estudiante traspasado exitosamente y notas eliminadas",
        "estudiante_id" => $estudiante_id,
        "curso_anterior" => $curso_actual,
        "curso_nuevo" => $nuevo_curso_id,
        "notas_eliminadas" => $totalNotas
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error al traspasar estudiante: " . $stmtUpdate->error
    ]);
}

$stmtEstudiante->close();
$stmtCurso->close();
$stmtUpdate->close();
$stmtCount->close();
$stmtDelete->close();
?>