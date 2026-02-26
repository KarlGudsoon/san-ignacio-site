<?php

require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";

$sql = "SELECT id, nombre FROM asignaturas ORDER BY nombre";
$result = $conexion->query($sql);

$asignaturas = [];

while ($row = $result->fetch_assoc()) {
    $asignaturas[] = $row;
}

echo json_encode($asignaturas);