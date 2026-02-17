<?php
require_once __DIR__ . '/../../middlewares/auth_admin.php';
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

$id_profesor = $_SESSION["user_id"];

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

    // 🔒 Verificar que ese curso_profesor pertenece al profesor logueado
    $sqlValidar = "SELECT id 
                   FROM curso_profesor 
                   WHERE id = ? AND profesor_id = ?";

    $stmtValidar = $conexion->prepare($sqlValidar);
    $stmtValidar->bind_param("ii", $curso_profesor_id, $id_profesor);
    $stmtValidar->execute();
    $resultValidar = $stmtValidar->get_result();

    if ($resultValidar->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No tienes permiso para crear evaluación en ese curso"
        ]);
        exit;
    }

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
