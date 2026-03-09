<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../middlewares/auth_estudiante.php";

if (!isset($_GET['curso_profesor_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Curso no especificado"
    ]);
    exit;
}

$curso_profesor_id = intval($_GET['curso_profesor_id']);
$estudiante_id = $_SESSION['estudiante_id'];

/* Validar que el estudiante pertenece al curso del docente */
$stmt = $conexion->prepare(
    "SELECT 
        *
    FROM curso_profesor cp
    INNER JOIN estudiantes e ON e.curso_id = cp.curso_id
    WHERE cp.id = ? AND e.id = ? LIMIT 1"
    );
$stmt->bind_param("ii", $curso_profesor_id, $estudiante_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

/* Obtener unidades */
$stmt = $conexion->prepare("
    SELECT id, nombre, descripcion, fecha_creacion
    FROM unidad
    WHERE curso_profesor_id = ?
    AND activo = 1
    ORDER BY fecha_creacion ASC
");
$stmt->bind_param("i", $curso_profesor_id);
$stmt->execute();
$result = $stmt->get_result();

$unidades = [];

while ($row = $result->fetch_assoc()) {
    $unidades[] = $row;
}

echo json_encode([
    "success" => true,
    "unidades" => $unidades
]);