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

$sql = "SELECT m.*, CONCAT( c.nivel, ' ' ,c.letra) AS curso, c.nivel, c.letra
    FROM matriculas_formulario m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    ORDER BY m.fecha_registro DESC";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->execute();

$result = $stmt->get_result();

$matriculas = [];

while ($row = $result->fetch_assoc()) {

    // Opcional: agregar edad calculada
    $row["edad"] = calcularEdad($row["fecha_nacimiento"]);

    $matriculas[] = $row;
}

$stmt->close();

echo json_encode([
    "success" => true,
    "matriculas" => $matriculas
]);