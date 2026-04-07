<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");


$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;
$tipo_estudiante = $_SESSION["tipo_estudiante"];

if ($tipo_estudiante == "presencial") {
    $sql = "SELECT 
        m.*,
        u.nombre AS unidad_nombre,
        c.nombre AS categoria_nombre,
        DATE_FORMAT(m.fecha_subida, '%d-%m-%Y') AS fecha_subida_formateada
    FROM material m
    INNER JOIN unidad u ON m.unidad_id = u.id
    INNER JOIN material_categoria c ON m.categoria_id = c.id
    WHERE m.curso_profesor_id = ?
    AND m.visible = 1
    ORDER BY u.fecha_creacion ASC, c.nombre ASC;";
} else {
    $sql = "SELECT 
        m.*,
        u.nombre AS unidad_nombre,
        c.nombre AS categoria_nombre,
        DATE_FORMAT(m.fecha_subida, '%d-%m-%Y') AS fecha_subida_formateada
    FROM material_distancia m
    INNER JOIN unidad_distancia u ON m.unidad_id = u.id
    INNER JOIN material_categoria c ON m.categoria_id = c.id
    WHERE m.curso_profesor_id = ?
    AND m.visible = 1
    ORDER BY u.fecha_creacion ASC, c.nombre ASC;";
} 

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $curso_profesor_id);
$stmt->execute();

$result = $stmt->get_result();

$material = [];

while ($row = $result->fetch_assoc()) {
    $material[] = $row;
}

echo json_encode([
    "success" => true,
    "material" => $material
]);
