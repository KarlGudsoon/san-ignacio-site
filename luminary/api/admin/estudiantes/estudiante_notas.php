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
FROM notas n
INNER JOIN evaluaciones e ON n.evaluacion_id = e.id
INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
INNER JOIN asignaturas a ON cp.asignatura_id = a.id
WHERE n.estudiante_id = ?
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

    $notasAgrupadas[$asignatura][] = [
        "nota" => (float)$row["nota"],
        "evaluacion_id" => (int)$row["evaluacion_id"],
        "fecha_aplicacion" => $row["fecha_aplicacion"]
    ];
}

echo json_encode([
    "success" => true,
    "notas" => $notasAgrupadas
]);