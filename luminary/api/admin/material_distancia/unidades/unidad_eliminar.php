<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../../config/db.php";

if (!isset($_POST['unidad_id'])) {
    echo json_encode(["success" => false, "message" => "Unidad no especificada"]);
    exit;
}

$unidad_id = intval($_POST['unidad_id']);

/* Obtener archivos para eliminarlos físicamente */
$stmt = $conexion->prepare("
    SELECT archivo FROM material_distancia WHERE unidad_id = ?
");
$stmt->bind_param("i", $unidad_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $ruta = __DIR__ . "/../../../uploads/material/" . $row['archivo'];
    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

/* Eliminar unidad (material se elimina por CASCADE) */
$stmt = $conexion->prepare("
    DELETE FROM unidad_distancia WHERE id = ?
");
$stmt->bind_param("i", $unidad_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Unidad eliminada correctamente"
]);