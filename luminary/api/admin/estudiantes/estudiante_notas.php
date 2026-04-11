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

$estudiante_id = $_GET["estudiante_id"] ?? null;

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
FROM estudiantes est
INNER JOIN curso_asignatura ca ON ca.curso_id = est.curso_id
INNER JOIN asignaturas a ON a.id = ca.asignatura_id
LEFT JOIN curso_profesor cp ON cp.asignatura_id = a.id AND cp.curso_id = ca.curso_id
LEFT JOIN evaluaciones e ON e.curso_profesor_id = cp.id
LEFT JOIN notas n ON n.evaluacion_id = e.id AND n.estudiante_id = est.id
WHERE est.id = ?
ORDER BY a.nombre, e.fecha_aplicacion;";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("i", $estudiante_id);
$stmt->execute();

$result = $stmt->get_result();

$notasAgrupadas = [];

while ($row = $result->fetch_assoc()) {

    $asignatura = $row["asignatura"];

    if (!isset($notasAgrupadas[$asignatura])) {
        $notasAgrupadas[$asignatura] = [];
    }

    $evaluacionId = $row["evaluacion_id"] !== null ? (int)$row["evaluacion_id"] : null;
    $nota = $row["nota"] !== null ? (float)$row["nota"] : null;
    $fechaAplicacion = $row["fecha_aplicacion"] ?? null;

    if ($evaluacionId !== null) {
        $notasAgrupadas[$asignatura][] = [
            "nota" => $nota,
            "evaluacion_id" => $evaluacionId,
            "fecha_aplicacion" => $fechaAplicacion
        ];
    }
}

echo json_encode([
    "success" => true,
    "notas" => $notasAgrupadas
]);