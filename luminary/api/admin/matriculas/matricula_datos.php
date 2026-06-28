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

$matricula_id = $_GET["id_matricula"];

if (!$matricula_id) {
    echo json_encode(["success" => false, "message" => "Falta id_matricula"]);
    exit;
}

$sql = "SELECT 
            *
        FROM matriculas_formulario mf
        WHERE mf.id = ?
        ";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}
$stmt->bind_param("i", $matricula_id);
$stmt->execute();
$matricula = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$matricula) {
    echo json_encode(["success" => false, "message" => "Matricula no encontrada"]);
    exit;
}

echo json_encode([
    "success" => true,
    "matricula" => $matricula
]);