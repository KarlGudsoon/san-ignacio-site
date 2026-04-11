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
            e.id AS id_estudiante,
            m.nombre_estudiante, 
            m.apellidos_estudiante, 
            m.rut_estudiante, 
            m.fecha_nacimiento, 
            m.situacion_especial_estudiante, 
            CONCAT(c.nivel,' ',c.letra) AS curso,
            m.telefono_estudiante, 
            m.correo_estudiante, 
            m.nombre_apoderado, 
            m.parentezco_apoderado, 
            m.telefono_apoderado
        FROM estudiantes e
        INNER JOIN matriculas m ON e.matricula_id = m.id
        INNER JOIN cursos c ON e.curso_id = c.id";

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

$estudiantes = [];

while ($row = $result->fetch_assoc()) {

    // Opcional: agregar edad calculada
    $row["edad"] = calcularEdad($row["fecha_nacimiento"]);

    $estudiantes[] = $row;
}

$stmt->close();

echo json_encode([
    "success" => true,
    "estudiantes" => $estudiantes
]);