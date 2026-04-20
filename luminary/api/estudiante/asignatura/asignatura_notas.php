<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$estudiante_id = $_SESSION['estudiante_id'] ?? null;
$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;

if (!$estudiante_id) {
    echo json_encode([
        "success" => false,
        "message" => "Error en parametros"
    ]);
    exit;
}

$sql = "SELECT 
	n.evaluacion_id AS evaluacion_id,
	e.titulo,
    e.descripcion,
    n.nota,
    DATE_FORMAT(e.fecha_aplicacion, '%d/%m/%Y') AS fecha_aplicacion,
    te.nombre AS tipo_evaluacion
FROM notas n 
INNER JOIN evaluaciones e ON e.id = n.evaluacion_id
INNER JOIN tipo_evaluacion te ON te.id = e.tipo_id
WHERE n.estudiante_id = ? AND e.curso_profesor_id = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("ii", $estudiante_id, $curso_profesor_id);
$stmt->execute();

$result = $stmt->get_result();

$evaluaciones = [];
while ($row = $result->fetch_assoc()) {
    $evaluaciones[] = $row;
}

$stmt->close();

// Calcular promedio
$promedio = null;
$notas = array_filter(
    array_column($evaluaciones, 'nota'),
    fn($n) => $n !== null && is_numeric($n)
);

if (count($notas) > 0) {
    $promedio = round(array_sum($notas) / count($notas), 1);
}

echo json_encode([
    "success" => true,
    "evaluaciones" => $evaluaciones,
    "promedio" => $promedio ?? "N/A"
]);