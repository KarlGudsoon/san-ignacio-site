<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
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

$curso_id = $_GET["curso_id"] ?? null;

if (!$curso_id) {
    echo json_encode(["success" => false, "message" => "Curso no especificado"]);
    exit;
}

$sqlAsignaturas = "SELECT a.nombre
    FROM curso_profesor cp
    INNER JOIN asignaturas a ON cp.asignatura_id = a.id
    WHERE cp.curso_id = ?
    GROUP BY a.nombre
    ORDER BY a.nombre";

$stmtAsig = $conexion->prepare($sqlAsignaturas);
$stmtAsig->bind_param("i", $curso_id);
$stmtAsig->execute();
$asignaturas = array_column(
    $stmtAsig->get_result()->fetch_all(MYSQLI_ASSOC),
    'nombre'
);
$stmtAsig->close();

// 1. Estudiantes
$sql = "SELECT 
            e.id AS id_estudiante,
            m.nombre_estudiante, 
            m.apellidos_estudiante, 
            m.rut_estudiante, 
            m.fecha_nacimiento, 
            m.situacion_especial_estudiante, 
            m.telefono_estudiante, 
            m.correo_estudiante, 
            m.nombre_apoderado, 
            m.parentezco_apoderado, 
            m.telefono_apoderado,
            m.tipo_estudiante,
            (
                SELECT COUNT(DISTINCT n.evaluacion_id)
                FROM notas n
                INNER JOIN evaluaciones ev ON n.evaluacion_id = ev.id
                INNER JOIN curso_profesor cp ON ev.curso_profesor_id = cp.id
                WHERE n.estudiante_id = e.id 
                AND cp.curso_id = e.curso_id
                AND n.nota = 'P'
            ) AS notas_pendientes
        FROM estudiantes e
        INNER JOIN matriculas m ON e.matricula_id = m.id
        WHERE e.curso_id = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en prepare"]);
    exit;
}
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$result = $stmt->get_result();

// 2. Promedios por asignatura
$sqlPromedios = "SELECT 
        e.id AS estudiante_id,
        a.nombre AS asignatura,
        SUM(CASE WHEN n.nota = 'P' THEN 1 ELSE 0 END) AS tiene_pendiente,
        AVG(CASE WHEN n.nota REGEXP '^[0-9]+(\\.[0-9]+)?\$' THEN CAST(n.nota AS DECIMAL(4,2)) END) AS promedio
    FROM estudiantes e
    CROSS JOIN curso_profesor cp ON cp.curso_id = e.curso_id
    INNER JOIN asignaturas a ON cp.asignatura_id = a.id
    LEFT JOIN evaluaciones ev ON ev.curso_profesor_id = cp.id
    LEFT JOIN notas n ON n.evaluacion_id = ev.id AND n.estudiante_id = e.id
    WHERE e.curso_id = ?
    GROUP BY e.id, a.nombre";

$stmtProm = $conexion->prepare($sqlPromedios);
$stmtProm->bind_param("i", $curso_id);
$stmtProm->execute();
$promediosRaw = $stmtProm->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtProm->close();

$promediosPorEstudiante = [];
foreach ($promediosRaw as $p) {
    if ((int)$p['tiene_pendiente'] > 0) {
        $valor = "P";
    } elseif ($p['promedio'] !== null) {
        $valor = number_format($p['promedio'], 1);
    } else {
        $valor = "N/A";
    }

    $promediosPorEstudiante[$p['estudiante_id']][$p['asignatura']] = $valor;
}

// 3. Armar respuesta
$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    $row["edad"] = calcularEdad($row["fecha_nacimiento"]);
    $row["promedios"] = $promediosPorEstudiante[$row["id_estudiante"]] ?? [];
    $estudiantes[] = $row;
}

$stmt->close();

echo json_encode([
    "success" => true,
    "asignaturas" => $asignaturas,
    "estudiantes" => $estudiantes
]);