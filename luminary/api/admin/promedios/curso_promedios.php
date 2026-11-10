<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

function calcularEdad($fecha_nacimiento)
{
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

// 1. Asignaturas del curso (una por curso_profesor)
$sqlAsignaturas = "SELECT 
        cp.id AS curso_profesor_id,
        a.nombre
    FROM curso_profesor cp
    INNER JOIN asignaturas a ON cp.asignatura_id = a.id
    WHERE cp.curso_id = ? AND a.nombre != 'Jefatura'
    ORDER BY a.id";

$stmtAsig = $conexion->prepare($sqlAsignaturas);
$stmtAsig->bind_param("i", $curso_id);
$stmtAsig->execute();
$asignaturas = $stmtAsig->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtAsig->close();

// 2. Estudiantes
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

// 3. Promedios anuales manuales (tabla promedios_asignaturas)
$sqlPromedios = "SELECT 
        pa.estudiante_id,
        pa.curso_profesor_id,
        pa.promedio_anual
    FROM promedios_asignaturas pa
    INNER JOIN curso_profesor cp ON cp.id = pa.curso_profesor_id
    WHERE cp.curso_id = ?";

$stmtProm = $conexion->prepare($sqlPromedios);
$stmtProm->bind_param("i", $curso_id);
$stmtProm->execute();
$promediosRaw = $stmtProm->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtProm->close();

$promediosPorEstudiante = [];
foreach ($promediosRaw as $p) {
    $valor = $p["promedio_anual"];

    if ($valor === null || $valor === "") {
        $valor = null;
    } elseif (strtoupper($valor) === "P") {
        $valor = "P";
    } elseif (is_numeric($valor)) {
        $valor = number_format((float)$valor, 1);
    }

    $promediosPorEstudiante[$p["estudiante_id"]][$p["curso_profesor_id"]] = $valor;
}

// 4. Armar respuesta
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
