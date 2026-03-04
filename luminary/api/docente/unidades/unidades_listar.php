<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../middlewares/auth_editor.php";

if (!isset($_GET['curso_profesor_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Curso no especificado"
    ]);
    exit;
}

$curso_profesor_id = intval($_GET['curso_profesor_id']);
$docente_id = $_SESSION['user_id'];

/* Validar que el curso pertenece al docente */
$stmt = $conexion->prepare("
    SELECT id FROM curso_profesor 
    WHERE id = ? AND profesor_id = ?
");
$stmt->bind_param("ii", $curso_profesor_id, $docente_id);
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