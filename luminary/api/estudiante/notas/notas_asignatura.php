<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");


$estudiante_id = $_SESSION["estudiante_id"] ?? null;

if (!$estudiante_id) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no especificado"
    ]);
    exit;
}

$sql = "SELECT 
    a.nombre AS asignatura,
    n.nota,
    n.evaluacion_id,
    e.fecha_aplicacion
FROM matriculas m
INNER JOIN cursos c ON m.curso_preferido = c.id
INNER JOIN curso_profesor cp ON cp.curso_id = c.id
INNER JOIN asignaturas a ON cp.asignatura_id = a.id
LEFT JOIN evaluaciones e ON e.curso_profesor_id = cp.id
LEFT JOIN notas n ON n.evaluacion_id = e.id AND n.estudiante_id = ?
WHERE m.estudiante_id = ?
ORDER BY a.nombre, e.fecha_aplicacion;";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("ii", $estudiante_id, $estudiante_id);
$stmt->execute();

$result = $stmt->get_result();

$notasAgrupadas = [];

while ($row = $result->fetch_assoc()) {
    $asignatura = $row["asignatura"];

    if (!isset($notasAgrupadas[$asignatura])) {
        $notasAgrupadas[$asignatura] = [];
    }

    if ($row["nota"] !== null) {
        $notasAgrupadas[$asignatura][] = [
            "nota" => is_numeric($row["nota"]) ? (float)$row["nota"] : $row["nota"], // 👈
            "evaluacion_id" => (int)$row["evaluacion_id"],
            "fecha_aplicacion" => $row["fecha_aplicacion"]
        ];
    }
}

// Calcular promedio general
$sumaGeneral = 0;
$cantidadGeneral = 0;

foreach ($notasAgrupadas as $notas) {
    foreach ($notas as $n) {
        if (is_numeric($n["nota"])) { // 👈
            $sumaGeneral += $n["nota"];
            $cantidadGeneral++;
        }
    }
}

$promedio = $cantidadGeneral > 0 ? round($sumaGeneral / $cantidadGeneral, 1) : null;

echo json_encode([
    "success" => true,
    "notas" => $notasAgrupadas,
    "promedio" => $promedio
]);