<?php
include '../conexion.php';
session_start();

if (!in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header("Location: ../index.html");
    exit;
}

$curso_id = $_GET['curso_id'] ?? null;
if (!$curso_id) {
    echo "ID del curso no proporcionado.";
    exit;
}

$formatter = new IntlDateFormatter(
    'es_CL',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Santiago',
    IntlDateFormatter::GREGORIAN,
    'd MMMM Y'
);
$fecha_formateada = $formatter->format(new DateTime());

// Obtener nivel y letra del curso
$curso_sql = $conexion->prepare("SELECT nivel, letra, profesor_jefe_id FROM cursos WHERE id = ?");
$curso_sql->bind_param("i", $curso_id);
$curso_sql->execute();
$curso_result = $curso_sql->get_result();

if ($curso_result->num_rows === 0) {
    echo "Curso no encontrado.";
    exit;
}

$curso = $curso_result->fetch_assoc();
$nivel = $curso['nivel'];
$letra = $curso['letra'];
$profesor_jefe_id = $curso['profesor_jefe_id'];

// Nombre del profesor jefe
$profesor_nombre = "_________________";
if ($profesor_jefe_id) {
    $profe_sql = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $profe_sql->bind_param("i", $profesor_jefe_id);
    $profe_sql->execute();
    $profe_result = $profe_sql->get_result();
    $profesor_nombre = $profe_result->fetch_assoc()['nombre'] ?? $profesor_nombre;
}

// Obtener todos los estudiantes del curso
$estudiantes_sql = $conexion->prepare("SELECT id, nombre, rut FROM estudiantes WHERE curso_id = ? ORDER BY nombre");
$estudiantes_sql->bind_param("i", $curso_id);
$estudiantes_sql->execute();
$estudiantes_result = $estudiantes_sql->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe del curso <?= htmlspecialchars($nivel . " " . $letra) ?></title>
</head>
<body>
    

<?php while ($estudiante = $estudiantes_result->fetch_assoc()): ?>
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
    
    <p>DON(ÑA)  <b><u style="text-transform: uppercase;"><?= htmlspecialchars($estudiante['nombre']) ?></u></b> RUT <b><u><?= htmlspecialchars($estudiante['rut']) ?></u></b> ALUMNO DEL <b><u><?= htmlspecialchars($nivel) ?> NIVEL <?= htmlspecialchars($letra)?></u></b> DE EDUCACIÓN MEDIA, DE ACUERDO A LAS DISPOSICIONES  REGLAMENTARIAS EN VIGENCIA, HA OBTENIDO LAS SIGUIENTES CALIFICACIONES DURANTE EL PRIMER SEMESTRE ACADÉMICO.</p>


    <?php
    // Obtener notas del estudiante
    $notas_sql = $conexion->prepare("
        SELECT a.nombre AS asignatura,
               n.nota1, n.nota2, n.nota3, n.nota4, n.nota5, n.nota6, n.nota7, n.nota8, n.nota9, n.x̄
        FROM curso_asignatura ca
        INNER JOIN asignaturas a ON ca.asignatura_id = a.id
        LEFT JOIN notas n ON a.id = n.asignatura_id AND n.estudiante_id = ?
        WHERE ca.curso_id = ?
        ORDER BY a.nombre
    ");
    $notas_sql->bind_param("ii", $estudiante['id'], $curso_id);
    $notas_sql->execute();
    $notas_result = $notas_sql->get_result();
    ?>

    <table class="tabla-notas" border="1" style="border-collapse: collapse; width: 100%; margin-top: 10px;">
        <thead>
            <tr>
                <th rowspan="2">ASIGNATURAS</th>
                <th colspan="9">NOTAS PARCIALES</th>
                <th rowspan="2">X</th>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= 9; $i++): ?>
                    <th><?= $i ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php if ($notas_result->num_rows > 0): ?>
            <?php while ($nota = $notas_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($nota['asignatura']) ?></td>
                <?php for ($i = 1; $i <= 9; $i++): ?>
                    <td><?= $nota["nota$i"] ?? '-' ?></td>
                <?php endfor; ?>
                <td><strong><?= $nota['x̄'] ?? '-' ?></strong></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="11">Sin asignaturas registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p><b>OBSERVACIONES:</b></p>
    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>

    <p style="text-align: right;">VILLA ALEMANA, <?= $fecha_formateada ?></p>

    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div style="text-align: center;">
            <p>___________________________</p>
            <p><?= htmlspecialchars($profesor_nombre) ?></p>
            <p style="margin: 0;">NOMBRE Y FIRMA</p>
            <p style="margin: 0;">PROFESOR(A) JEFE</p>
        </div>
        <div style="text-align: center;">
            <p>___________________________</p>
            <p>Francisco Pinochet Gatica</p>
            <p style="margin: 0;">NOMBRE, APELLIDOS, Y TIMBRE</p>
            <p style="margin: 0;">DIRECTOR</p>
        </div>
    </div>
    <div class="page-break"></div>
<?php endwhile; ?>
</body>
</html>

<style>
    .tabla-notas td, .tabla-notas th {
        text-align: center;
        font-size: 12px;
        padding: 4px;
    }

    .page-break {
        page-break-after: always;
        break-after: page;
    }
</style>

<script>
    window.onload = function () {
        window.print();
    };
</script>

