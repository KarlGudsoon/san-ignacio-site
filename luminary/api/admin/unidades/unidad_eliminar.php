<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../middlewares/auth_editor.php";

if (!isset($_POST['unidad_id'])) {
    echo json_encode(["success" => false, "message" => "Unidad no especificada"]);
    exit;
}

$unidad_id = intval($_POST['unidad_id']);
$docente_id = $_SESSION['user_id'];

/* Validar que la unidad pertenece al docente */
$stmt = $conexion->prepare("
    SELECT u.id, u.curso_profesor_id
    FROM unidad u
    INNER JOIN curso_profesor cp ON u.curso_profesor_id = cp.id
    WHERE u.id = ? AND cp.profesor_id = ?
");
$stmt->bind_param("ii", $unidad_id, $docente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

/* Obtener archivos para eliminarlos físicamente */
$stmt = $conexion->prepare("
    SELECT archivo FROM material WHERE unidad_id = ?
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
    DELETE FROM unidad WHERE id = ?
");
$stmt->bind_param("i", $unidad_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Unidad eliminada correctamente"
]);