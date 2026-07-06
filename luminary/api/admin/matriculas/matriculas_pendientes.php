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

$sqlMatriculasP = "SELECT m.*, CONCAT( c.nivel, ' ' ,c.letra) AS curso, c.nivel, c.letra
    FROM matriculas_formulario m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    ORDER BY m.fecha_registro DESC";

$stmt = $conexion->prepare($sqlMatriculasP);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->execute();

$result = $stmt->get_result();

$matriculas_pendientes = [];
$matriculas_activas = [];

while ($row = $result->fetch_assoc()) {

    if (!empty($row["fecha_registro"])) {
        $row["fecha_registro"] = (new DateTime($row["fecha_registro"]))->format("d-m-Y H:i");
    }

    // Opcional: agregar edad calculada
    $row["edad"] = calcularEdad($row["fecha_nacimiento"]);

    if ($row["estado"] === "Activa") {
        $matriculas_activas[] = $row;
    } else {
        $matriculas_pendientes[] = $row;
    }
}

$stmt->close();

echo json_encode([
    "success" => true,
    "matriculas_pendientes" => $matriculas_pendientes,
    "cantidad" => count($matriculas_pendientes),
    "matriculas_activas" => $matriculas_activas
]);