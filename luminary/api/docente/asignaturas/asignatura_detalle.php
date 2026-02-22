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
$id_curso_profesor = $_GET["id"];

$sql = "SELECT 
            cp.id,
            c.nivel AS curso_nivel,
            CONCAT(c.nivel,' Nivel ',c.letra) AS curso,
            a.nombre AS nombre
        FROM curso_profesor cp
        INNER JOIN cursos c ON c.id = cp.curso_id
        INNER JOIN asignaturas a ON a.id = cp.asignatura_id
        WHERE cp.profesor_id = ? AND cp.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_profesor, $id_curso_profesor);
$stmt->execute();

$result = $stmt->get_result();
$asignatura = $result->fetch_assoc(); // 👈 SOLO UNO

echo json_encode([
    "success" => true,
    "asignatura" => $asignatura
]);