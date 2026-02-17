<?php
require_once __DIR__ . "/../../middlewares/auth_admin.php";
require_once __DIR__ . "/../../config/db.php";

header("Content-Type: application/json");

$evaluacion_id = $_GET["evaluacion_id"] ?? null;

if (!$evaluacion_id) {
    echo json_encode(["error" => "Falta evaluacion_id"]);
    exit;
}

$sql = "
SELECT 
    e.id AS estudiante_id,
    m.nombre_estudiante,
    n.nota
FROM estudiantes e
INNER JOIN matriculas m ON m.id = e.matricula_id
INNER JOIN curso_profesor cp 
    ON cp.curso_id = e.curso_id
INNER JOIN evaluaciones ev 
    ON ev.curso_profesor_id = cp.id
LEFT JOIN notas n 
    ON n.estudiante_id = e.id 
    AND n.evaluacion_id = ev.id
WHERE ev.id = ?;
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $evaluacion_id);
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];

while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row;
}

echo json_encode([
    "success" => true,
    "estudiantes" => $estudiantes
]);
