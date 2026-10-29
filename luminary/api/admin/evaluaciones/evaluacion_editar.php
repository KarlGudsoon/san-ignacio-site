<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";

header("Content-Type: application/json; charset=utf-8");

// 1. Validar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
    exit;
}

// 2. Verificar sesión de administrador
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

// 3. Capturar y sanitizar entradas
$evaluacion_id = filter_input(INPUT_POST, 'evaluacion_id', FILTER_VALIDATE_INT);
$nombre = trim($_POST["titulo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = trim($_POST["fecha_aplicacion"] ?? "");

if (!$evaluacion_id || empty($nombre) || empty($fecha)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios o el ID de la evaluación no es válido"]);
    exit;
}

// Habilitar excepciones en MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 4. Verificar únicamente que la evaluación exista en la base de datos
    $sqlValidar = "SELECT id FROM evaluaciones WHERE id = ?";
    $stmtValidar = $conexion->prepare($sqlValidar);
    $stmtValidar->bind_param("i", $evaluacion_id);
    $stmtValidar->execute();

    if ($stmtValidar->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "La evaluación especificada no existe"]);
        exit;
    }

    // 5. Editar la evaluación
    $sqlEditar = "UPDATE evaluaciones SET titulo = ?, descripcion = ?, fecha_aplicacion = ? WHERE id = ?";
    $stmtEditar = $conexion->prepare($sqlEditar);
    $stmtEditar->bind_param("sssi", $nombre, $descripcion, $fecha, $evaluacion_id);
    $stmtEditar->execute();

    echo json_encode([
        "success" => true,
        "message" => "Evaluación editada correctamente"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error interno al editar la evaluación"
    ]);
}