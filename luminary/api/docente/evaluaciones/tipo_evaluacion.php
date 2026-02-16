<?php
require_once __DIR__ . '/../../middlewares/auth_admin.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$sql = "SELECT id, nombre FROM tipo_evaluacion ORDER BY nombre ASC";
$result = $conexion->query($sql);

$tipos = [];

while ($row = $result->fetch_assoc()) {
    $tipos[] = $row;
}

echo json_encode([
    "success" => true,
    "tipos" => $tipos
]);
