<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_profesor = $_SESSION["user_id"];
$evaluacion_id = $_POST["evaluacion_id"] ?? null;

$nombre = $_POST["titulo"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";
$fecha = $_POST["fecha_aplicacion"] ?? "";

if (!$evaluacion_id) {
    echo json_encode(["success" => false, "message" => "Falta evaluacion_id"]);
    exit;
}

try {
    // 🔒 Verificar que la evaluación pertenece a un curso del profesor logueado
    $sqlValidar = "SELECT e.id 
                   FROM evaluaciones e
                   INNER JOIN curso_profesor cp ON cp.id = e.curso_profesor_id
                   WHERE e.id = ? AND cp.profesor_id = ?";

    $stmtValidar = $conexion->prepare($sqlValidar);
    $stmtValidar->bind_param("ii", $evaluacion_id, $id_profesor);
    $stmtValidar->execute();
    $resultValidar = $stmtValidar->get_result();

    if ($resultValidar->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "No tienes permiso para editar esta evaluación"]);
        exit;
    }

    // 📝 Editar la evaluación
    $sqlEditar = "UPDATE evaluaciones SET titulo = ?, descripcion = ?, fecha_aplicacion = ? WHERE id = ?";
    $stmtEditar = $conexion->prepare($sqlEditar);
    $stmtEditar->bind_param("sssi", $nombre, $descripcion, $fecha, $evaluacion_id);
    $stmtEditar->execute();

    echo json_encode([
        "success" => true,
        "message" => "Evaluación editada correctamente"
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al editar evaluación"]);
}