<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;

$sql = "SELECT 
            cp.id, 
            a.nombre AS nombre_asignatura,
            u.nombre AS profesor_asignatura
        FROM curso_profesor cp 
        INNER JOIN asignaturas a ON cp.asignatura_id = a.id
        INNER JOIN usuarios u ON u.id = cp.profesor_id
        WHERE cp.id = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("i", $curso_profesor_id);
$stmt->execute();

$result = $stmt->get_result();

$asignatura = $result->fetch_assoc();

$stmt->close();

echo json_encode([
    "success" => true,
    "asignatura" => $asignatura
]);