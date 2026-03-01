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

$curso_id = $_GET["curso_id"] ?? null;

if (!$curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "Curso no especificado"
    ]);
    exit;
}

$sql_asig = "SELECT 
                a.id AS asignatura_id,
                a.nombre AS asignatura,
                cp.id AS curso_profesor_id,
                cp.profesor_id,
                u.nombre AS nombre_profesor
            FROM curso_profesor cp
            INNER JOIN asignaturas a ON cp.asignatura_id = a.id
            INNER JOIN usuarios u ON cp.profesor_id = u.id
            WHERE cp.curso_id = ?";

$stmt1 = $conexion->prepare($sql_asig);
$stmt1->bind_param("i", $curso_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$asignaturas = $result1->fetch_all(MYSQLI_ASSOC);
$stmt1->close();

echo json_encode([
    "success" => true,
    "asignaturas" => $asignaturas
]);