<?php
require_once __DIR__ . '/../../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

$estudiante_id = $_SESSION["estudiante_id"] ?? null;

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
    e.titulo AS evaluacion_nombre,
    e.fecha_aplicacion,
    e.semestre
FROM matriculas m
INNER JOIN cursos c ON m.curso_preferido = c.id
INNER JOIN curso_profesor cp ON cp.curso_id = c.id
INNER JOIN asignaturas a ON cp.asignatura_id = a.id
LEFT JOIN evaluaciones e ON e.curso_profesor_id = cp.id
LEFT JOIN notas n ON n.evaluacion_id = e.id AND n.estudiante_id = ?
WHERE m.estudiante_id = ? AND a.nombre != 'Jefatura'
ORDER BY a.nombre, e.fecha_aplicacion;";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Error en prepare"
    ]);
    exit;
}

$stmt->bind_param("ii", $estudiante_id, $estudiante_id);
$stmt->execute();

$result = $stmt->get_result();

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

$notasAgrupadas = [];

while ($row = $result->fetch_assoc()) {
    $asignatura = $row["asignatura"];

    if (!isset($notasAgrupadas[$asignatura])) {
        $notasAgrupadas[$asignatura] = [];
    }

    if ($row["nota"] !== null) {
        $notasAgrupadas[$asignatura][] = [
            "nota" => is_numeric($row["nota"]) ? (float)$row["nota"] : $row["nota"],
            "evaluacion_id" => (int)$row["evaluacion_id"],
            "evaluacion_nombre" => $row["evaluacion_nombre"],
            "fecha_aplicacion" => $row["fecha_aplicacion"],
            "semestre" => $row["semestre"]
        ];
    }
}

// Calcular promedios por asignatura y general del año
$asignaturas = [];
$promediosFinalesAsignaturas = [];
$promediosSem1Anio = [];
$promediosSem2Anio = [];
$cantidadPendientes = 0;

foreach ($notasAgrupadas as $nombreAsignatura => $notas) {
    $sem1 = [];
    $sem2 = [];

    foreach ($notas as $n) {
        if (!is_numeric($n["nota"])) {
            if ($n["nota"] === "P") {
                $cantidadPendientes++;
            }
            continue;
        }

        $valor = (float)$n["nota"];

        if ((int)$n["semestre"] === 1) {
            $sem1[] = $valor;
        } elseif ((int)$n["semestre"] === 2) {
            $sem2[] = $valor;
        }
    }

    $promSem1 = promedio($sem1);
    $promSem2 = promedio($sem2);
    $promGeneral = promedio(array_values(array_filter(
        [$promSem1, $promSem2],
        fn($p) => $p !== null
    )));

    $asignaturas[$nombreAsignatura] = [
        "notas" => $notas,
        "promedio_semestre_1" => $promSem1,
        "promedio_semestre_2" => $promSem2,
        "promedio_general" => $promGeneral
    ];

    if ($promSem1 !== null) {
        $promediosSem1Anio[] = $promSem1;
    }

    if ($promSem2 !== null) {
        $promediosSem2Anio[] = $promSem2;
    }

    if ($promGeneral !== null) {
        $promediosFinalesAsignaturas[] = $promGeneral;
    }
}

$promedioGeneralSem1 = promedio($promediosSem1Anio);
$promedioGeneralSem2 = promedio($promediosSem2Anio);
$promedioGeneralAnio = promedio($promediosFinalesAsignaturas);

echo json_encode([
    "success" => true,
    "asignaturas" => $asignaturas,
    "promedio_general_semestre_1" => $promedioGeneralSem1,
    "promedio_general_semestre_2" => $promedioGeneralSem2,
    "promedio_general_anio" => $promedioGeneralAnio,
    "cantidad_pendientes" => $cantidadPendientes
], JSON_UNESCAPED_UNICODE);