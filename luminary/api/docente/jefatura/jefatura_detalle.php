<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_editor.php';
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
    c.id,
    c.nivel AS curso_nivel,
    CONCAT(c.nivel,' Nivel ',c.letra) AS curso
FROM cursos c
WHERE c.profesor_jefe_id = ?;";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_profesor);
$stmt->execute();

$result = $stmt->get_result();

$jefatura = $result->fetch_assoc();

if (!$jefatura) {
    echo json_encode([
        "success" => false,
        "message" => "No tienes jefatura asignada"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "jefatura" => $jefatura
]);