<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/luminary/dompdf/autoload.inc.php';
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_admin2.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

use Dompdf\Dompdf;

$id_curso = intval($_GET['id_curso'] ?? 0);
if (!$id_curso) exit("Curso inválido");

// Validar si se requiere filtrar por un semestre específico (1 o 2)
$semestre_param = isset($_GET['semestre']) ? intval($_GET['semestre']) : null;
$semestres_mostrar = in_array($semestre_param, [1, 2], true) ? [$semestre_param] : [1, 2];

// Nombres legibles para los semestres
$nombres_semestres = [
    1 => 'PRIMER SEMESTRE',
    2 => 'SEGUNDO SEMESTRE'
];

// ============================================================
//  Profesor jefe (se obtiene 1 sola vez para todo el curso)
// ============================================================
$profesor_jefe_nombre = "";
$query_profesor_jefe = "
    SELECT u.nombre
    FROM cursos c
    INNER JOIN usuarios u ON c.profesor_jefe_id = u.id
    WHERE c.id = ?
    LIMIT 1
";
$stmt = $conexion->prepare($query_profesor_jefe);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profesor_jefe = $result->fetch_assoc();
    $profesor_jefe_nombre = $profesor_jefe['nombre'];
}
$stmt->close();

// ============================================================
//  Obtener todos los estudiantes del curso
// ============================================================
$query_estudiantes = "
    SELECT m.*, c.nivel, c.letra, c.id AS curso_id
    FROM matriculas m
    INNER JOIN cursos c ON m.curso_preferido = c.id
    WHERE c.id = ?
";
$stmt = $conexion->prepare($query_estudiantes);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$estudiantes_result = $stmt->get_result();

if ($estudiantes_result->num_rows === 0) exit("No hay estudiantes matriculados en este curso");

$fecha_hoy = date("d/m/Y");

function calcularEdad($fecha_nacimiento) {
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($fecha_nac)->y;
}

function promedioInforme(array $valores, int $decimales = 1)
{
    if (count($valores) === 0) {
        return null;
    }

    $promedio = array_sum($valores) / count($valores);
    $factor = pow(10, $decimales);

    return floor($promedio * $factor + 0.5 + 1e-9) / $factor;
}

// Filtro SQL para la tabla de evaluaciones según el semestre
$filtro_semestre_sql = "";
if (count($semestres_mostrar) === 1) {
    $filtro_semestre_sql = " AND (e.semestre = " . $semestres_mostrar[0] . " OR e.id IS NULL)";
}

// ============================================================
//  Recolectar estudiantes y sus notas por semestre
// ============================================================
$estudiantes = [];

while ($d = $estudiantes_result->fetch_assoc()) {

    $id = $d['estudiante_id'];
    $curso_id = intval($d['curso_id']);

    // ------- Consulta de notas del estudiante -------
    $notas_query = "
        SELECT 
            a.nombre AS asignatura,
            n.nota,
            e.semestre
        FROM curso_profesor cp
        INNER JOIN asignaturas a     ON cp.asignatura_id   = a.id
        LEFT JOIN evaluaciones e     ON e.curso_profesor_id = cp.id $filtro_semestre_sql
        LEFT JOIN notas n            ON n.evaluacion_id    = e.id
                                    AND n.estudiante_id    = ?
        WHERE cp.curso_id = ? AND a.nombre != 'Jefatura'
        ORDER BY a.nombre, e.semestre, e.fecha_aplicacion ASC
    ";
    $stmt_notas = $conexion->prepare($notas_query);
    $stmt_notas->bind_param("ii", $id, $curso_id);
    $stmt_notas->execute();
    $notas_result = $stmt_notas->get_result();

    $notas_por_asignatura = [];
    while ($row = $notas_result->fetch_assoc()) {
        $asignatura = $row['asignatura'];

        if (!isset($notas_por_asignatura[$asignatura])) {
            $notas_por_asignatura[$asignatura] = [];
            foreach ($semestres_mostrar as $s) {
                $notas_por_asignatura[$asignatura][$s] = [];
            }
        }

        if ($row['nota'] !== null) {
            $semestre = (int)$row['semestre'];
            if (in_array($semestre, $semestres_mostrar, true)) {
                $notas_por_asignatura[$asignatura][$semestre][] = $row['nota'];
            }
        }
    }
    $stmt_notas->close();

    $estudiantes[] = [
        'd' => $d,
        'notas' => $notas_por_asignatura
    ];
}

// ------- Columnas dinámicas para todo el curso -------
$maxPorSem = [];
foreach ($semestres_mostrar as $s) {
    $maxPorSem[$s] = 6; // Valor mínimo predeterminado
}

foreach ($estudiantes as $est) {
    foreach ($semestres_mostrar as $s) {
        foreach ($est['notas'] as $notas) {
            $maxPorSem[$s] = max($maxPorSem[$s], count($notas[$s] ?? []));
        }
    }
}

$colspanTotal = 1 + array_sum($maxPorSem) + 1;

// Encabezados dinámicos (thead)
$thead_html = '<tr>'
    . '<th colspan="1" rowspan="2">ASIGNATURAS</th>';

foreach ($semestres_mostrar as $s) {
    $thead_html .= '<th colspan="' . $maxPorSem[$s] . '">' . $nombres_semestres[$s] . '</th>';
}

$thead_html .= '<th rowspan="2">PROMEDIO</th>'
    . '</tr><tr>';

foreach ($semestres_mostrar as $s) {
    for ($i = 1; $i <= $maxPorSem[$s]; $i++) {
        $thead_html .= '<th>' . $i . '</th>';
    }
}

$thead_html .= '</tr>';

// Subtítulo dinámico para los informes
$titulo_semestre = (count($semestres_mostrar) === 1) 
    ? mb_strtoupper($nombres_semestres[$semestres_mostrar[0]]) 
    : 'ANUAL';

// ============================================================
//  Generar el HTML concatenado de todos los estudiantes
// ============================================================
$html_completo = '<body>';
$primero = true;

foreach ($estudiantes as $est) {

    $d = $est['d'];
    $id = $d['estudiante_id'];
    $edad = calcularEdad($d["fecha_nacimiento"]);
    $curso_id = intval($d['curso_id']);
    $notas_por_asignatura = $est['notas'];

    // ------- Tbody de notas -------
    $tbody_html = '';
    if (empty($notas_por_asignatura)) {
        $tbody_html = '<tr><td colspan="' . $colspanTotal . '" style="text-align:center;">Sin notas registradas</td></tr>';
    } else {
        foreach ($notas_por_asignatura as $asignatura => $notas) {
            $promSem = [];

            foreach ($semestres_mostrar as $s) {
                $notasNumericas = array_values(array_filter(
                    $notas[$s] ?? [],
                    fn($n) => is_numeric($n)
                ));
                $promSem[$s] = promedioInforme(array_map('floatval', $notasNumericas));
            }

            $promGeneral = promedioInforme(array_values(array_filter(
                $promSem,
                fn($p) => $p !== null
            )));

            $tbody_html .= '<tr>';
            $tbody_html .= '<td style="text-transform: uppercase;">' . htmlspecialchars($asignatura) . '</td>';

            foreach ($semestres_mostrar as $s) {
                for ($i = 0; $i < $maxPorSem[$s]; $i++) {
                    $nota = $notas[$s][$i] ?? null;

                    if ($nota === null) {
                        $valor = ' - ';
                    } elseif (is_numeric($nota)) {
                        $valor = number_format((float)$nota, 1);
                    } else {
                        $valor = htmlspecialchars(strtoupper($nota));
                    }

                    $tbody_html .= '<td style="text-align:center;">' . $valor . '</td>';
                }
            }

            $tbody_html .= '<td style="text-align:center;font-weight:bold;">'
                . ($promGeneral !== null ? number_format($promGeneral, 1) : '')
                . '</td>';
            $tbody_html .= '</tr>';
        }
    }

    // ------- Salto de página entre fichas -------
    $page_break = $primero ? '' : 'page-break-before: always;';
    $primero = false;

    // ------- HTML individual del estudiante -------
    $html_completo .= '
    <div style="' . $page_break . '">
        <h1 style="text-align: center; margin-bottom: 5px;">INFORME DE NOTAS ' . $titulo_semestre . '</h1>
        <p style="text-align: center; margin: 2px;">ENSEÑANZA MEDIA HUMANÍSTICO – CIENTÍFICA</p>
        <p style="text-align: center; margin: 2px;">CENTRO DE ESTUDIOS “SAN IGNACIO”</p>

        <table border="1" style="border-collapse: collapse; width: 100%; margin-top: 15px;">
            <thead>
                <tr>
                    <td>
                        <p style="margin: 0; font-size: 10px;">REGIÓN</p>
                        <p style="margin: 0;">VALPARAISO</p>
                    </td>
                    <td>
                        <p style="margin: 0; font-size: 10px;">PROVINCIA</p>
                        <p style="margin: 0;">VALPARAISO</p>
                    </td>
                    <td>
                        <p style="margin: 0; font-size: 10px;">COMUNA</p>
                        <p style="margin: 0;">VILLA ALEMANA</p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p style="margin: 0; font-size: 10px;">DECRETO EVALUACION Y PROMOCIÓN ESCOLAR</p>
                        <p style="margin: 0;">2169/2007</p>
                    </td>
                    <td>
                        <p style="margin: 0; font-size: 10px;">DECRETO PLANES Y PROGRAMAS DE ESTUDIO</p>
                        <p style="margin: 0;">1000/2009</p>
                    </td>
                    <td>
                        <p style="margin: 0; font-size: 10px;">RESOLUCION EXENTA</p>
                        <p style="margin: 0;">N° 000844 DE 1998</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 15px;">DON(ÑA) <b><u style="text-transform: uppercase;">' . htmlspecialchars($d["nombre_estudiante"] . " " . $d["apellidos_estudiante"]) . '</u></b> RUT <b><u>' . htmlspecialchars($d["rut_estudiante"] ?: "Sin información") . '</u></b> ALUMNO DEL <b><u>' . htmlspecialchars($d["nivel"] . " NIVEL " . $d["letra"] ?: "Sin información") . '</u></b> DE EDUCACIÓN MEDIA, DE ACUERDO A LAS DISPOSICIONES REGLAMENTARIAS EN VIGENCIA, HA OBTENIDO LAS SIGUIENTES CALIFICACIONES DURANTE EL ' . (count($semestres_mostrar) === 1 ? 'SEMESTRE ACADÉMICO' : 'AÑO ACADÉMICO') . '.</p>

        <table class="tabla-notas" border="1" style="border-collapse: collapse; width: 100%;">
            <thead>' . $thead_html . '</thead>
            <tbody>' . $tbody_html . '</tbody>
        </table>

        <p style="margin-top: 15px;"><b>OBSERVACIONES</b></p>
        <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>
        <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>
        <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>

        <p style="text-transform: uppercase; text-align: right; width: 100%; margin-top: 20px;">VILLA ALEMANA, ' . $fecha_hoy . '</p>

        <div class="firma" style="margin-top: 30px;">
            <div class="firma-izquierda" style="float: left; width: 50%; text-align: center;">
                <p style="margin: 0;">___________________________</p>
                <p style="margin: 0;">' . $profesor_jefe_nombre . '</p>
                <p style="margin: 0;">NOMBRE Y FIRMA</p>
                <p style="margin: 0;">PROFESOR(A) JEFE</p>
            </div>
            <div class="firma-derecha" style="float: right; width: 50%; text-align: center;">
                <p style="margin: 0;">___________________________</p>
                <p style="margin: 0;">Francisco Pinochet Gatica</p>
                <p style="margin: 0;">NOMBRE, APELLIDOS, Y TIMBRE</p>
                <p style="margin: 0;">DIRECTOR</p>
            </div>
        </div>
    </div>
    ';
}

$html_completo .= '</body>';

// ============================================================
//  Generar el PDF único con todos los estudiantes
// ============================================================
$dompdf = new Dompdf([
    "isRemoteEnabled" => true
]);
$dompdf->loadHtml($html_completo);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$nombre_archivo = count($semestres_mostrar) === 1 
    ? "Informes_Curso_{$id_curso}_Semestre_{$semestres_mostrar[0]}.pdf" 
    : "Informes_Curso_{$id_curso}_Anual.pdf";

$dompdf->stream($nombre_archivo, ["Attachment" => false]);
exit;