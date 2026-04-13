<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";

$evaluacion_id = $_POST["evaluacion_id"];
$estudiante_id = $_POST["estudiante_id"];
$nota = $_POST["nota"];

// Verificar si ya existe
// Si la nota está vacía, eliminar el registro
if (empty($nota)) {
    $sqlDelete = "DELETE FROM notas WHERE evaluacion_id = ? AND estudiante_id = ?";
    $stmtDelete = $conexion->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $evaluacion_id, $estudiante_id);
    $stmtDelete->execute();
    echo json_encode(["success" => true]);
    exit;
}

// Verificar si ya existe
$sqlCheck = "SELECT id FROM notas 
             WHERE evaluacion_id = ? AND estudiante_id = ?";

$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $evaluacion_id, $estudiante_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {

    $sqlUpdate = "UPDATE notas 
                  SET nota = ? 
                  WHERE evaluacion_id = ? AND estudiante_id = ?";

    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sii", $nota, $evaluacion_id, $estudiante_id);
    $stmtUpdate->execute();

} else {

    $sqlInsert = "INSERT INTO notas (evaluacion_id, estudiante_id, nota)
                  VALUES (?, ?, ?)";

    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->bind_param("iss", $evaluacion_id, $estudiante_id, $nota);
    $stmtInsert->execute();
}

echo json_encode(["success" => true]);
