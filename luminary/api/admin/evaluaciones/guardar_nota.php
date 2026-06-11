<?php
require_once __DIR__ . "/../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../config/db.php";

$evaluacion_id = $_POST["evaluacion_id"];
$estudiante_id = $_POST["estudiante_id"];
$nota = $_POST["nota"];

// Consultar si la evaluación es coeficiente 2
$sqlCoef = "SELECT coeficiente2 FROM evaluaciones WHERE id = ?";
$stmtCoef = $conexion->prepare($sqlCoef);
$stmtCoef->bind_param("i", $evaluacion_id);
$stmtCoef->execute();
$resultCoef = $stmtCoef->get_result();
$evaluacion = $resultCoef->fetch_assoc();
$esCoef2 = $evaluacion && $evaluacion['coeficiente2'] == 1;

// Eliminar SIEMPRE las filas existentes para este estudiante/evaluación
// (puede haber 1 o 2 dependiendo del coeficiente anterior)
$sqlDelete = "DELETE FROM notas WHERE evaluacion_id = ? AND estudiante_id = ?";
$stmtDelete = $conexion->prepare($sqlDelete);
$stmtDelete->bind_param("ii", $evaluacion_id, $estudiante_id);
$stmtDelete->execute();

// Si la nota está vacía, solo se eliminó y listo
if (empty($nota)) {
    echo json_encode(["success" => true]);
    exit;
}

// Insertar 1 o 2 filas según coeficiente
$sqlInsert = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
$stmtInsert = $conexion->prepare($sqlInsert);
$stmtInsert->bind_param("iis", $evaluacion_id, $estudiante_id, $nota);
$stmtInsert->execute();

if ($esCoef2) {
    // Segunda inserción — misma nota, vale doble
    $stmtInsert->execute();
}

echo json_encode(["success" => true]);
