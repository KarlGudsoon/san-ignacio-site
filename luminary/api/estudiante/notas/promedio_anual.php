<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$estudiante_id = $_SESSION["estudiante_id"] ?? null;
$curso_id = $_SESSION["curso_id"] ?? null;

if (!$estudiante_id || !$curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no especificado"
    ]);
    exit;
}

/**
 * Promedio con redondeo a 1 decimal: centésima >= 5 sube.
 * El epsilon corrige el ruido de punto flotante.
 */
function promedio(array $valores, int $decimales = 1)
{
    if (count($valores) === 0) {
        return null;
    }

    $promedio = array_sum($valores) / count($valores);
    $factor = pow(10, $decimales);

    return floor($promedio * $factor + 0.5 + 1e-9) / $factor;
}

// 1. Asignaturas del curso con el promedio anual manual del estudiante.
//    Se toma el último registro (MAX id) porque no hay índice único
//    en (estudiante_id, curso_profesor_id).
$sql = "SELECT 
        cp.id AS curso_profesor_id,
        a.nombre,
        pa.promedio_anual
    FROM curso_profesor cp
    INNER JOIN asignaturas a ON a.id = cp.asignatura_id
    LEFT JOIN promedios_asignaturas pa
        ON pa.curso_profesor_id = cp.id
        AND pa.estudiante_id = ?
        AND pa.id = (
            SELECT MAX(pa2.id)
            FROM promedios_asignaturas pa2
            WHERE pa2.estudiante_id = pa.estudiante_id
              AND pa2.curso_profesor_id = pa.curso_profesor_id
        )
    WHERE cp.curso_id = ? AND a.nombre != 'Jefatura'
    ORDER BY a.id";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("ii", $estudiante_id, $curso_id);
$stmt->execute();
$result = $stmt->get_result();

$asignaturas = [];
$valores = [];
$hayPendiente = false;

while ($row = $result->fetch_assoc()) {
    $valor = $row["promedio_anual"];

    if ($valor === null || trim((string)$valor) === "") {
        // Sin promedio registrado: no se puede calcular el final.
        $valor = null;
        $hayPendiente = true;
    } elseif (strtoupper($valor) === "P") {
        $valor = "P";
        $hayPendiente = true;
    } else {
        $valor = number_format((float)$valor, 1);
        $valores[] = (float)$valor;
    }

    $asignaturas[] = [
        "curso_profesor_id" => (int)$row["curso_profesor_id"],
        "nombre" => $row["nombre"],
        "promedio_anual" => $valor
    ];
}

$stmt->close();

// 2. Promedio anual: solo se calcula si todas las asignaturas tienen nota.
if (count($asignaturas) === 0) {
    $promedioAnual = null;
} elseif ($hayPendiente) {
    $promedioAnual = "P";
} else {
    $promedioAnual = number_format(promedio($valores), 1);
}

echo json_encode([
    "success" => true,
    "promedio_anual" => $promedioAnual,
    "asignaturas" => $asignaturas
], JSON_UNESCAPED_UNICODE);
