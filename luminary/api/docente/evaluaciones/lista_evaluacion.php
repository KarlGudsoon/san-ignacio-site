<?php
require_once __DIR__ . '/../../middlewares/auth_admin.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = [
    "success" => false,
    "error" => null,
    "debug" => []
];

// 🔐 Validar sesión
if (!isset($_SESSION["user_id"])) {
    $response["error"] = "Sesión no iniciada";
    echo json_encode($response);
    exit;
}

$id_profesor = $_SESSION["user_id"];
$curso_profesor_id = $_GET["curso_profesor_id"] ?? null;

$response["debug"]["id_profesor"] = $id_profesor;
$response["debug"]["curso_profesor_id"] = $curso_profesor_id;

if (!$curso_profesor_id) {
    $response["error"] = "Falta curso_profesor_id";
    echo json_encode($response);
    exit;
}

// 🔎 Validar que el curso pertenece al profesor
$sqlValidar = "SELECT id 
               FROM curso_profesor 
               WHERE id = ? AND profesor_id = ?";

$stmtValidar = $conexion->prepare($sqlValidar);

if (!$stmtValidar) {
    $response["error"] = "Error en prepare validar";
    $response["debug"]["sql_error"] = $conexion->error;
    echo json_encode($response);
    exit;
}

$stmtValidar->bind_param("ii", $curso_profesor_id, $id_profesor);
$stmtValidar->execute();
$resultValidar = $stmtValidar->get_result();

$response["debug"]["filas_validacion"] = $resultValidar->num_rows;

if ($resultValidar->num_rows === 0) {
    $response["error"] = "El curso no pertenece al profesor o no existe";
    echo json_encode($response);
    exit;
}

// 📚 Traer evaluaciones
$sql = "SELECT e.id, e.titulo, e.fecha_aplicacion, t.nombre AS tipo
        FROM evaluaciones e
        INNER JOIN tipo_evaluacion t ON t.id = e.tipo_id
        WHERE e.curso_profesor_id = ?
        ORDER BY e.fecha_aplicacion DESC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $response["error"] = "Error en prepare evaluaciones";
    $response["debug"]["sql_error"] = $conexion->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $curso_profesor_id);
$stmt->execute();
$result = $stmt->get_result();

$evaluaciones = [];

while ($row = $result->fetch_assoc()) {
    $evaluaciones[] = $row;
}

$response["success"] = true;
$response["evaluaciones"] = $evaluaciones;

echo json_encode($response);
