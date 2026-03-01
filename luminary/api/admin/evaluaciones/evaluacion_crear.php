<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

// Validar sesión
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

// Obtener datos enviados
$titulo = $_POST["titulo"] ?? null;
$descripcion = $_POST["descripcion"] ?? null;
$curso_profesor_id = $_POST["curso_profesor_id"] ?? null;
$tipo_id = $_POST["tipo_id"] ?? null;
$fecha = $_POST["fecha_aplicacion"] ?? null;

// Validación básica
if (!$titulo || !$curso_profesor_id || !$tipo_id) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan datos obligatorios"
    ]);
    exit;
}

try {
    // ✅ Insertar evaluación
    $sql = "INSERT INTO evaluaciones 
            (titulo, descripcion, curso_profesor_id, tipo_id, fecha_aplicacion) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssiis", $titulo, $descripcion, $curso_profesor_id, $tipo_id, $fecha);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "evaluacion_id" => $stmt->insert_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al guardar evaluación"
    ]);
}
