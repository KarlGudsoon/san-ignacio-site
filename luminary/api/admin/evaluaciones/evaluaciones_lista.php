<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

// 🔐 Validar sesión
if (!isset($_SESSION["user_id"])) {
    $response["error"] = "Sesión no iniciada";
    echo json_encode($response);
    exit;
}

$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;

// 📚 Traer evaluaciones
$sql = "SELECT e.id, e.titulo, e.fecha_aplicacion, t.nombre AS tipo
        FROM evaluaciones e
        INNER JOIN tipo_evaluacion t ON t.id = e.tipo_id
        WHERE e.curso_profesor_id = ?
        ORDER BY e.fecha_aplicacion ASC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $curso_profesor_id);
$stmt->execute();
$result = $stmt->get_result();

$evaluaciones = [];

while ($row = $result->fetch_assoc()) {
    $evaluaciones[] = $row;
}

$response["success"] = true;
$response["evaluaciones"] = $evaluaciones;

echo json_encode($response);