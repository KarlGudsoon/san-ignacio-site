<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/luminary/dompdf/autoload.inc.php';
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_admin2.php';

use Dompdf\Dompdf;

if (!isset($_GET['id'])) exit("ID inválido");
$id = intval($_GET['id']);

// Validar si se requiere filtrar por un semestre específico (1 o 2)
$semestre_param = isset($_GET['semestre']) ? intval($_GET['semestre']) : null;
$semestres_mostrar = in_array($semestre_param, [1, 2], true) ? [$semestre_param] : [1, 2];

$profesor_jefe_nombre = "";
$query_profesor_jefe = "
    SELECT u.nombre
    FROM cursos c
    INNER JOIN estudiantes e ON e.curso_id = c.id
    INNER JOIN usuarios u ON c.profesor_jefe_id = u.id
    WHERE e.id = ?
    LIMIT 1
";
$stmt = $conexion->prepare($query_profesor_jefe);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profesor_jefe = $result->fetch_assoc();
    $profesor_jefe_nombre = $profesor_jefe['nombre'];
}
$stmt->close();

// Obtener datos de matrícula
$query = "SELECT m.*, c.nivel, c.letra, c.id as curso_id
    FROM matriculas m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    WHERE m.estudiante_id = $id
    LIMIT 1
";
$result = $conexion->query($query);

if ($result->num_rows === 0) exit("Matrícula no encontrada");
$d = $result->fetch_assoc();

$fecha_hoy = date("d/m/Y");

function calcularEdad($fecha_nacimiento) {
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($fecha_nac)->y;
}
$edad = calcularEdad($d["fecha_nacimiento"]);

// ============================================================
//  Obtener notas agrupadas por asignatura
// ============================================================
$curso_id = intval($d['curso_id']);

// Filtrar por semestre en la consulta SQL si aplica
$filtro_semestre_sql = "";
if (count($semestres_mostrar) === 1) {
    $filtro_semestre_sql = " AND (e.semestre = " . $semestres_mostrar[0] . " OR e.id IS NULL)";
}

$notas_query = "
    SELECT 
        a.nombre AS asignatura,
        n.nota,
        e.semestre
    FROM curso_profesor cp
    INNER JOIN asignaturas a     ON cp.asignatura_id   = a.id
    LEFT JOIN evaluaciones e     ON e.curso_profesor_id = cp.id $filtro_semestre_sql
    LEFT JOIN notas n            ON n.evaluacion_id    = e.id
                                AND n.estudiante_id    = $id
    WHERE cp.curso_id = $curso_id AND a.nombre != 'Jefatura'
    ORDER BY a.nombre, e.semestre, e.fecha_aplicacion ASC
";
$notas_result = $conexion->query($notas_query);

function promedioInforme(array $valores, int $decimales = 1)
{
    if (count($valores) === 0) {
        return null;
    }

    $promedio = array_sum($valores) / count($valores);
    $factor = pow(10, $decimales);

    return floor($promedio * $factor + 0.5 + 1e-9) / $factor;
}

// Agrupar notas por asignatura y semestre
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

// Determinar número máximo de notas a desplegar por semestre
$maxPorSem = [];
foreach ($semestres_mostrar as $s) {
    $maxPorSem[$s] = 6; // Valor mínimo por defecto
}

foreach ($notas_por_asignatura as $notas) {
    foreach ($semestres_mostrar as $s) {
        $maxPorSem[$s] = max($maxPorSem[$s], count($notas[$s] ?? []));
    }
}

// Cálculo del Colspan total
$colspanTotal = 1 + array_sum($maxPorSem) + 1;

// Nombres legibles para semestres
$nombres_semestres = [
    1 => 'PRIMER SEMESTRE',
    2 => 'SEGUNDO SEMESTRE'
];

// Generar filas HTML del tbody
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

// Generar encabezados (thead)
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

// Subtítulo dinámico para el PDF
$titulo_semestre = (count($semestres_mostrar) === 1) 
    ? mb_strtoupper($nombres_semestres[$semestres_mostrar[0]]) 
    : 'ANUAL';

// ==========================
//   HTML del PDF
// ==========================
$html = '
<body>
    <h1 style="text-align: center; margin-bottom: 5px;">INFORME DE NOTAS '. $titulo_semestre .'</h1>
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

    <p style="margin-top: 15px;">DON(ÑA) <b><u style="text-transform: uppercase;">'.htmlspecialchars($d["nombre_estudiante"]." ".$d["apellidos_estudiante"]).'</u></b> RUT <b><u>'.htmlspecialchars($d["rut_estudiante"] ?: "Sin información").'</u></b> ALUMNO DEL <b><u>'.htmlspecialchars($d["nivel"] . " NIVEL " . $d["letra"] ?: "Sin información").'</u></b> DE EDUCACIÓN MEDIA, DE ACUERDO A LAS DISPOSICIONES REGLAMENTARIAS EN VIGENCIA, HA OBTENIDO LAS SIGUIENTES CALIFICACIONES DURANTE EL '. (count($semestres_mostrar) === 1 ? 'SEMESTRE ACADÉMICO' : 'AÑO ACADÉMICO') .'.</p>

    <table class="tabla-notas" border="1" style="border-collapse: collapse; width: 100%;">
        <thead>' . $thead_html . '</thead>
        <tbody>' . $tbody_html . '</tbody>
    </table>

    <p style="margin-top: 15px;"><b>OBSERVACIONES</b></p>
    <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase; margin: 2px;">___________________________________________________________________________________________</p>

    <p style="text-transform: uppercase; text-align: right; width: 100%; margin-top: 20px;">VILLA ALEMANA, '.$fecha_hoy.'</p>

    <div class="firma" style="margin-top: 30px;">
        <div class="firma-izquierda" style="float: left; width: 50%; text-align: center;">
            <p style="margin: 0;">___________________________</p>
            <p style="margin: 0;">'.$profesor_jefe_nombre.'</p>
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
</body>
';

// ==========================
//   Generar el PDF
// ==========================
$dompdf = new Dompdf([
    "isRemoteEnabled" => true
]);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$nombre_archivo = count($semestres_mostrar) === 1 
    ? "Informe_Notas_Semestre_{$semestres_mostrar[0]}_$id.pdf" 
    : "Informe_Notas_Anual_$id.pdf";

$dompdf->stream($nombre_archivo, ["Attachment" => false]);
exit;
?>