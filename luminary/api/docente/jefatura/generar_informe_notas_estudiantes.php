<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/luminary/dompdf/autoload.inc.php';
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_editor.php';

use Dompdf\Dompdf;

$id_curso = intval($_GET['id_curso']);
if (!$id_curso) exit("Curso inválido");

// ============================================================
//  Profesor jefe (esto es igual para todo el curso, se saca 1 sola vez)
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

// ============================================================
//  Ir generando el HTML de cada estudiante y concatenarlo
// ============================================================
$html_completo = '<body>';
$primero = true;

while ($d = $estudiantes_result->fetch_assoc()) {

    $id = $d['estudiante_id']; // ajusta el nombre real de la columna si es distinto
    $edad = calcularEdad($d["fecha_nacimiento"]);
    $curso_id = intval($d['curso_id']);

    // ------- Notas del estudiante -------
    $notas_query = "
        SELECT 
            a.nombre AS asignatura,
            n.nota
        FROM curso_profesor cp
        INNER JOIN asignaturas a     ON cp.asignatura_id   = a.id
        LEFT JOIN evaluaciones e     ON e.curso_profesor_id = cp.id
        LEFT JOIN notas n            ON n.evaluacion_id    = e.id
                                    AND n.estudiante_id    = ?
        WHERE cp.curso_id = ? AND a.nombre != 'Jefatura'
        ORDER BY a.nombre, e.fecha_aplicacion ASC
    ";
    $stmt_notas = $conexion->prepare($notas_query);
    $stmt_notas->bind_param("ii", $id, $curso_id);
    $stmt_notas->execute();
    $notas_result = $stmt_notas->get_result();

    $notas_por_asignatura = [];
    while ($row = $notas_result->fetch_assoc()) {
        if (!isset($notas_por_asignatura[$row['asignatura']])) {
            $notas_por_asignatura[$row['asignatura']] = [];
        }
        if ($row['nota'] !== null) {
            $notas_por_asignatura[$row['asignatura']][] = $row['nota'];
        }
    }
    $stmt_notas->close();

    // ------- Tbody de notas -------
    $tbody_html = '';
    if (empty($notas_por_asignatura)) {
        $tbody_html = '<tr><td colspan="14" style="text-align:center;">Sin notas registradas</td></tr>';
    } else {
        foreach ($notas_por_asignatura as $asignatura => $notas) {
            $notas_numericas = array_filter($notas, fn($n) => is_numeric($n));
            $promedio = count($notas_numericas) > 0
                ? array_sum($notas_numericas) / count($notas_numericas)
                : null;

            $tbody_html .= '<tr>';
            $tbody_html .= '<td style="text-transform: uppercase;">' . htmlspecialchars($asignatura) . '</td>';

            for ($i = 0; $i < 12; $i++) {
                if (!isset($notas[$i])) {
                    $valor = ' - ';
                } elseif (is_numeric($notas[$i])) {
                    $valor = number_format($notas[$i], 1);
                } else {
                    $valor = htmlspecialchars(strtoupper($notas[$i]));
                }
                $tbody_html .= '<td style="text-align:center;">' . $valor . '</td>';
            }

            $tbody_html .= '<td style="text-align:center;font-weight:bold;">'
                . ($promedio !== null ? number_format($promedio, 1) : '')
                . '</td>';
            $tbody_html .= '</tr>';
        }
    }

    // ------- Salto de página entre estudiantes (menos antes del primero) -------
    $page_break = $primero ? '' : 'page-break-before: always;';
    $primero = false;

    // ------- HTML individual del estudiante -------
    $html_completo .= '
    <div style="' . $page_break . '">
        <h1 style="text-align: center;">INFORME DE NOTAS SEMESTRAL</h1>
        <p style="text-align: center;">ENSEÑANZA MEDIA HUMANÍSTICO – CIENTÍFICA</p>
        <p style="text-align: center;">CENTRO DE ESTUDIOS “SAN IGNACIO”</p>

        <table border="1" style="border-collapse: collapse; width: 100%;">
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
        <p>DON(ÑA) <b><u style="text-transform: uppercase;">' . htmlspecialchars($d["nombre_estudiante"] . " " . $d["apellidos_estudiante"]) . '</u></b> RUT <b><u>' . htmlspecialchars($d["rut_estudiante"] ?: "Sin información") . '</u></b> ALUMNO DEL <b><u>' . htmlspecialchars($d["nivel"] . " NIVEL " . $d["letra"] ?: "Sin información") . '</u></b> DE EDUCACIÓN MEDIA, DE ACUERDO A LAS DISPOSICIONES  REGLAMENTARIAS EN VIGENCIA, HA OBTENIDO LAS SIGUIENTES CALIFICACIONES DURANTE EL SEMESTRE ACADÉMICO.</p>

        <table class="tabla-notas" border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th rowspan="2">ASIGNATURAS</th>
                    <th colspan="13">NOTAS PARCIALES</th>
                </tr>
                <tr>
                    <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th>
                    <th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th><th>X</th>
                </tr>
            </thead>
            <tbody>' . $tbody_html . '</tbody>
        </table>
        <p><b>OBSERVACIONES</b></p>

        <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
        <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
        <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>

        <p style="text-transform: uppercase; text-align: right; margin-top: 20px;">VILLA ALEMANA, ' . $fecha_hoy . '</p>

        <div class="firma">
            <div class="firma-izquierda" style="float: left; width: 50%; text-align: center; margin-top: 20px;">
                <p>___________________________</p>
                <p style="margin: 0;">' . $profesor_jefe_nombre . '</p>
                <p style="margin: 0;">NOMBRE Y FIRMA</p>
                <p style="margin: 0;">PROFESOR(A) JEFE</p>
            </div>
            <div class="firma-derecha" style="float: right; width: 50%; text-align: center; margin-top: 20px;">
                <p>___________________________</p>
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
//  Generar UN SOLO PDF con todos los estudiantes
// ============================================================
$dompdf = new Dompdf([
    "isRemoteEnabled" => true
]);
$dompdf->loadHtml($html_completo);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("InformesCurso_$id_curso.pdf", ["Attachment" => false]);
exit;