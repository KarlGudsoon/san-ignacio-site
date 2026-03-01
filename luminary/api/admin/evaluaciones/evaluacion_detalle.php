<?php
require_once __DIR__ . "/../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../config/db.php";

header("Content-Type: application/json");

$evaluacion_id = $_GET["evaluacion_id"] ?? null;

if (!$evaluacion_id) {
    echo json_encode(["error" => "Falta evaluacion_id"]);
    exit;
}

//////////////////////////////////////////////////
// 1️⃣ TRAER DETALLE DE LA EVALUACIÓN
//////////////////////////////////////////////////

$sqlEval = "SELECT e.id, e.titulo, e.descripcion, te.nombre AS tipo_evaluacion, a.nombre AS asignatura, CONCAT(c.nivel,' Nivel ',c.letra) AS curso, c.nivel, e.fecha_aplicacion
            FROM evaluaciones e
            INNER JOIN tipo_evaluacion te ON te.id = e.tipo_id
            INNER JOIN curso_profesor cp ON cp.id = e.curso_profesor_id
            INNER JOIN asignaturas a ON a.id = cp.asignatura_id
            INNER JOIN cursos c ON c.id = cp.curso_id
            WHERE e.id = ?";

$stmtEval = $conexion->prepare($sqlEval);
$stmtEval->bind_param("i", $evaluacion_id);
$stmtEval->execute();
$resultEval = $stmtEval->get_result();
$evaluacion = $resultEval->fetch_assoc();

//////////////////////////////////////////////////
// 2️⃣ TRAER ESTUDIANTES + NOTAS
//////////////////////////////////////////////////

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

//////////////////////////////////////////////////
// 3️⃣ RESPUESTA FINAL
//////////////////////////////////////////////////

echo json_encode([
    "success" => true,
    "evaluacion" => $evaluacion,
    "estudiantes" => $estudiantes
]);
