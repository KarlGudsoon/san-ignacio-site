<?php
include '../conexion.php';
session_start();

$formatter = new IntlDateFormatter(
    'es_CL', // Localización chilena en español
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Santiago',
    IntlDateFormatter::GREGORIAN,
    'd MMMM Y'
);
$fecha_formateada = $formatter->format(new DateTime());

if (!in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header("Location: ../index.html");
    exit;
}

$estudiante_id = $_GET['id'] ?? null;
if (!$estudiante_id) {
    echo "ID de estudiante no proporcionado.";
    exit;
}

// Obtener información del estudiante
$estudiante_sql = $conexion->prepare("SELECT m.nombre_estudiante, m.apellidos_estudiante, m.rut_estudiante FROM estudiantes e INNER JOIN matriculas m ON e.matricula_id = m.id WHERE e.id = ?");
$estudiante_sql->bind_param("i", $estudiante_id);
$estudiante_sql->execute();
$estudiante_result = $estudiante_sql->get_result();

if ($estudiante_result->num_rows === 0) {
    echo "Estudiante no encontrado.";
    exit;
}

$estudiante = $estudiante_result->fetch_assoc();

// Obtener el curso del estudiante
$curso_id_sql = $conexion->prepare("SELECT curso_id FROM estudiantes WHERE id = ?");
$curso_id_sql->bind_param("i", $estudiante_id);
$curso_id_sql->execute();
$curso_id_result = $curso_id_sql->get_result();
$curso_id = $curso_id_result->fetch_assoc()['curso_id'] ?? null;

$profesor_jefe_id = null;

if ($curso_id) {
    $profesor_sql = $conexion->prepare("SELECT profesor_jefe_id FROM cursos WHERE id = ?");
    $profesor_sql->bind_param("i", $curso_id);
    $profesor_sql->execute();
    $profesor_result = $profesor_sql->get_result();

    if ($profesor_result->num_rows > 0) {
        $profesor_jefe_id = $profesor_result->fetch_assoc()['profesor_jefe_id'] ?? null;
    }
}

if ($profesor_jefe_id) {
    $nombre_sql = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $nombre_sql->bind_param("i", $profesor_jefe_id);
    $nombre_sql->execute();
    $nombre_result = $nombre_sql->get_result();

    if ($nombre_result->num_rows > 0) {
        $profesor_jefe_nombre = $nombre_result->fetch_assoc()['nombre'];
    }
}

if ($curso_id) {
    $nivel_sql = $conexion->prepare("SELECT nivel FROM cursos WHERE id = ?");
    $nivel_sql->bind_param("i", $curso_id);
    $nivel_sql->execute();
    $nivel_result = $nivel_sql->get_result();

    if ($nivel_result->num_rows > 0) {
        $nivel_estudiante = $nivel_result->fetch_assoc()['nivel'] ?? null;
    }
}

if ($nivel_estudiante) {
    $letra_sql = $conexion->prepare("SELECT letra FROM cursos WHERE id = ?");
    $letra_sql->bind_param("i", $curso_id);
    $letra_sql->execute();
    $letra_result = $letra_sql->get_result();

    if ($letra_result->num_rows > 0) {
        $letra_estudiante = $letra_result->fetch_assoc()['letra'] ?? null;
    }
}


// Obtener asignaturas y notas del estudiante
$notas_sql = $conexion->prepare("
    SELECT a.nombre AS asignatura,
           n.nota1, n.nota2, n.nota3, n.nota4, n.nota5, n.nota6, n.nota7, n.nota8, n.nota9, n.x̄
    FROM curso_asignatura ca
    INNER JOIN asignaturas a ON ca.asignatura_id = a.id
    LEFT JOIN notas n ON a.id = n.asignatura_id AND n.estudiante_id = ?
    WHERE ca.curso_id = ?
    ORDER BY a.nombre
");
$notas_sql->bind_param("ii", $estudiante_id, $curso_id);
$notas_sql->execute();
$notas_result = $notas_sql->get_result();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de <?= htmlspecialchars($estudiante['nombre_estudiante']. ' ' . $estudiante['apellidos_estudiante']) ?></title>
</head>
<body>
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
    <p>DON(ÑA) <b><u style="text-transform: uppercase;"><?= htmlspecialchars($estudiante['nombre_estudiante']. ' ' .$estudiante['apellidos_estudiante']) ?></u></b> RUT <b><u><?= htmlspecialchars($estudiante['rut_estudiante']) ?></u></b> ALUMNO DEL <b><u><?= htmlspecialchars($nivel_estudiante) ?> NIVEL <?= htmlspecialchars($letra_estudiante)?></u></b> DE EDUCACIÓN MEDIA, DE ACUERDO A LAS DISPOSICIONES  REGLAMENTARIAS EN VIGENCIA, HA OBTENIDO LAS SIGUIENTES CALIFICACIONES DURANTE EL PRIMER SEMESTRE ACADÉMICO.</p>

    <table class="tabla-notas" border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th rowspan="2">ASIGNATURAS</th>
                <th colspan="9">NOTAS PARCIALES</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>X</th>
            </tr>
        </thead>
        <?php if ($notas_result->num_rows > 0): ?>
            <?php while ($nota = $notas_result->fetch_assoc()): ?>
            <tr>
                <td style="text-transform: uppercase;"><?= htmlspecialchars($nota['asignatura']) ?></td>
                <td class="nota"><?= $nota['nota1'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota2'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota3'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota4'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota5'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota6'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota7'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota8'] ?? '-' ?></td>
                <td class="nota"><?= $nota['nota9'] ?? '-' ?></td>
                <td class="nota"><strong><?= $nota['x̄'] ?? '-' ?></strong></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No hay notas registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
    <p><b>OBSERVACIONES</b></p>

    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>
    <p style="text-transform: uppercase;">___________________________________________________________________________________________</p>

    <p style="text-transform: uppercase; float: right; width: 100%; text-align: right; margin-top: 20px;">VILLA ALEMANA, <?= $fecha_formateada ?></p>

    <div class="firma">
        <div class="firma-izquierda" style="float: left; width: 50%; text-align: center; margin-top: 20px;">
            <p>___________________________</p>
            <p style="margin: 0;"><?= htmlspecialchars($profesor_jefe_nombre) ?></p>
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

    
</body>
</html>

<style>
    .tabla-notas .nota {
        text-align: center;
    }
</style>

<script>
    window.onload = function () {
        window.print();
    };
</script>