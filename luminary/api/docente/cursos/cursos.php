<?php
session_start();
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

$id_profesor = $_SESSION["user_id"];

$sql = "SELECT 
            cp.id,
            c.nivel AS curso_nivel,
            c.letra AS curso_letra,
            a.nombre AS asignatura
        FROM curso_profesor cp
        INNER JOIN cursos c ON c.id = cp.curso_id
        INNER JOIN asignaturas a ON a.id = cp.asignatura_id
        WHERE cp.profesor_id = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_profesor);
$stmt->execute();

$result = $stmt->get_result();

$cursos = [];

while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

echo json_encode([
    "success" => true,
    "cursos" => $cursos
]);
