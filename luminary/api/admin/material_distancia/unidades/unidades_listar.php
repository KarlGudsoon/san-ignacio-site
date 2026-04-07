<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../../config/db.php";

if (!isset($_GET['curso_profesor_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Curso no especificado"
    ]);
    exit;
}

$curso_profesor_id = intval($_GET['curso_profesor_id']);

/* Obtener unidades */
$stmt = $conexion->prepare("
    SELECT id, nombre, descripcion, fecha_creacion
    FROM unidad_distancia
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