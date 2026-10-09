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

$estudiante_id = $_GET["estudiante_id"] ?? null;

if (!$estudiante_id) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no especificado"
    ]);
    exit;
}

$sql = "SELECT 
    a.nombre AS asignatura,
    n.nota,
    n.evaluacion_id,
    e.fecha_aplicacion,
    e.semestre,
    e.titulo AS evaluacion_nombre
FROM estudiantes est
INNER JOIN curso_asignatura ca ON ca.curso_id = est.curso_id
INNER JOIN asignaturas a ON a.id = ca.asignatura_id
LEFT JOIN curso_profesor cp ON cp.asignatura_id = a.id AND cp.curso_id = ca.curso_id
LEFT JOIN evaluaciones e ON e.curso_profesor_id = cp.id
LEFT JOIN notas n ON n.evaluacion_id = e.id AND n.estudiante_id = est.id
WHERE est.id = ? AND a.nombre != 'Jefatura'
ORDER BY a.nombre, e.fecha_aplicacion;";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("i", $estudiante_id);
$stmt->execute();

$result = $stmt->get_result();

/**
 * Promedio con redondeo a 1 decimal: centésima >= 5 sube (6.25 → 6.3).
 * El epsilon corrige el ruido de punto flotante (6.2499999999 → 6.3).
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

$notasAgrupadas = [];

while ($row = $result->fetch_assoc()) {
    $asignatura = $row["asignatura"];

    if (!isset($notasAgrupadas[$asignatura])) {
        $notasAgrupadas[$asignatura] = [];
    }

    if ($row["nota"] !== null) {
        $notasAgrupadas[$asignatura][] = [
            "nota" => is_numeric($row["nota"]) ? (float)$row["nota"] : $row["nota"],
            "evaluacion_nombre" => $row["evaluacion_nombre"],
            "evaluacion_id" => (int)$row["evaluacion_id"],
            "semestre" => $row["semestre"],
            "fecha_aplicacion" => $row["fecha_aplicacion"]
        ];
    }
}

// ===== Cálculo de promedios =====
$asignaturas = [];
$promediosFinalesAsignaturas = []; // para el promedio general del año

foreach ($notasAgrupadas as $nombreAsignatura => $notas) {
    $sem1 = [];
    $sem2 = [];
    $todas = [];

    foreach ($notas as $n) {
        // Solo se promedian notas numéricas
        if (!is_numeric($n["nota"])) {
            continue;
        }

        $valor = (float)$n["nota"];
        $todas[] = $valor;

        if ((int)$n["semestre"] === 1) {
            $sem1[] = $valor;
        } elseif ((int)$n["semestre"] === 2) {
            $sem2[] = $valor;
        }
    }

    $promSem1 = promedio($sem1);
    $promSem2 = promedio($sem2);
    $promGeneral = promedio($todas);

    $asignaturas[$nombreAsignatura] = [
        "notas" => $notas,
        "promedio_semestre_1" => $promSem1,
        "promedio_semestre_2" => $promSem2,
        "promedio_general" => $promGeneral
    ];

    if ($promGeneral !== null) {
        $promediosFinalesAsignaturas[] = $promGeneral;
    }
}

// Promedio general del año = promedio de los promedios generales de cada asignatura
$promedioGeneralAnio = promedio($promediosFinalesAsignaturas);

echo json_encode([
    "success" => true,
    "asignaturas" => $asignaturas,
    "promedio_general_anio" => $promedioGeneralAnio
]);