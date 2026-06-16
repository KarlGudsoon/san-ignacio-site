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

function calcularEdad($fecha_nacimiento) {
    if (empty($fecha_nacimiento) || $fecha_nacimiento === '0000-00-00') {
        return 'Sin información';
    }
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

$sql = "SELECT 
            e.id,
            e.nombre,
            e.correo,
            e.asignatura
        FROM usuarios e
        WHERE e.rol = 'editor'
        ORDER BY e.nombre
        ";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->execute();
$docentes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    "success" => true,
    "docentes" => $docentes
]);