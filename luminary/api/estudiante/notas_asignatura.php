<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../api/config/db.php";

$estudiante_id = $_SESSION['estudiante_id'] ?? null;
$asignatura_id = $_GET["asignatura_id"] ?? null;

if (!$asignatura_id) {
  http_response_code(400);
  echo json_encode(["error" => "Asignatura requerida"]);
  exit;
}

$sql = "
SELECT 
    e.id AS evaluacion_id, 
    e.titulo AS evaluacion, 
    tp.nombre AS tipo_evaluacion, 
    n.nota, 
    e.fecha_aplicacion
FROM notas n
INNER JOIN evaluaciones e ON e.id = n.evaluacion_id 
INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
INNER JOIN tipo_evaluacion tp ON e.tipo_id = tp.id
WHERE n.estudiante_id = ? AND cp.asignatura_id = ?
ORDER BY e.fecha_aplicacion ASC;
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $estudiante_id, $asignatura_id);
$stmt->execute();

$resultado = $stmt->get_result();

$datos = [];
$suma = 0;
$cantidad = 0;

while ($row = $resultado->fetch_assoc()) {
    $datos[] = $row;

    if ($row["nota"] !== null) {
        $suma += floatval($row["nota"]);
        $cantidad++;
    }
}

$promedio = $cantidad > 0 ? round($suma / $cantidad, 1) : null;

echo json_encode([
    "success" => true,
    "evaluaciones" => $datos,
    "promedio" => $promedio
]);
