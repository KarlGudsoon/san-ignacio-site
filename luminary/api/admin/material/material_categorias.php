<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$sql = "SELECT 
	id,
    nombre,
    descripcion
FROM material_categoria";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$result = $stmt->get_result();

$categorias = [];
while ($row = $result->fetch_assoc()) {
    $categorias[] = $row;
}

echo json_encode([
    "success" => true,
    "categorias" => $categorias
]);
