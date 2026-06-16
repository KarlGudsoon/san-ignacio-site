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

$docente_id = $_GET["id_docente"];

if (!$docente_id) {
    echo json_encode(["success" => false, "message" => "Falta id_docente"]);
    exit;
}

$sql = "SELECT 
            e.id,
            e.nombre,
            e.correo,
            e.asignatura
        FROM usuarios e
        WHERE e.id = ?
        ";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$docente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$docente) {
    echo json_encode(["success" => false, "message" => "Docente no encontrado"]);
    exit;
}

echo json_encode([
    "success" => true,
    "docente" => $docente
]);