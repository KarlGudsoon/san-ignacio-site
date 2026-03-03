<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");


$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;

$sql = "SELECT m.*, c.nombre AS categoria_nombre
FROM material m
LEFT JOIN material_categoria c ON m.categoria_id = c.id
WHERE m.curso_profesor_id = ?
ORDER BY c.nombre, m.fecha_subida DESC";

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
