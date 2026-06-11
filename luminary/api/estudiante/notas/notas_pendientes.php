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
    e.fecha_aplicacion,
    e.titulo,
    e.descripcion,
    e.coeficiente2
FROM matriculas m
INNER JOIN cursos c ON m.curso_preferido = c.id
INNER JOIN curso_profesor cp ON cp.curso_id = c.id
INNER JOIN asignaturas a ON cp.asignatura_id = a.id
LEFT JOIN evaluaciones e ON e.curso_profesor_id = cp.id
LEFT JOIN notas n ON n.evaluacion_id = e.id AND n.estudiante_id = ?
WHERE m.estudiante_id = ? AND n.nota = 'P'
GROUP BY a.nombre, n.evaluacion_id, e.fecha_aplicacion
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

    $notasAgrupadas[$asignatura][] = [
        "evaluacion_id" => (int)$row["evaluacion_id"],
        "titulo" => $row["titulo"],
        "descripcion" => $row["descripcion"],
        "coeficiente" => $row["coeficiente2"] == 1 ? "Coef. 2" : "Coef. 1",
        "nota" => $row["nota"],
        "fecha_aplicacion" => $row["fecha_aplicacion"] ? date("d-m-Y", strtotime($row["fecha_aplicacion"])) : null
    ];
}

$evaluacionesUnicas = [];
foreach ($notasAgrupadas as $notas) {
    foreach ($notas as $n) {
        $evaluacionesUnicas[$n["evaluacion_id"]] = true;
    }
}

echo json_encode([
    "success" => true,
    "cantidad" => count($evaluacionesUnicas),
    "notas" => $notasAgrupadas
]);