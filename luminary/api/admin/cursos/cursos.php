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

$sql = "SELECT 
            c.id,
            c.nivel AS curso_nivel,
            CONCAT(c.nivel, ' Nivel ',c.letra) AS curso,
            c.jornada
        FROM cursos c";

$stmt = $conexion->prepare($sql);
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
