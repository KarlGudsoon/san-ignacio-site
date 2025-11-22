<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: /index.html");
    exit;
}

$curso_id = $_GET['curso_id'] ?? null;
$profesor_id = $_SESSION['id'];

if (!$curso_id) {
    die("ID de curso no proporcionado.");
}

// Verificar que el profesor es jefe del curso
$verificacion = $conexion->prepare("SELECT id FROM cursos WHERE id = ? AND profesor_jefe_id = ?");
$verificacion->bind_param("ii", $curso_id, $profesor_id);
$verificacion->execute();
$resultado_verif = $verificacion->get_result();

if ($resultado_verif->num_rows === 0) {
    die("No tiene permiso para descargar las notas de este curso.");
}

// Obtener nombre del curso para el archivo
$curso_info = $conexion->prepare("SELECT nivel, letra FROM cursos WHERE id = ?");
$curso_info->bind_param("i", $curso_id);
$curso_info->execute();
$curso = $curso_info->get_result()->fetch_assoc();

$nombre_archivo = "notas_curso_{$curso['nivel']}{$curso['letra']}.csv";

// Encabezados para descarga
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$nombre_archivo");

// Abrir la salida
$output = fopen('php://output', 'w');
$sep = ';';

// Escribir encabezados
fputcsv($output, ['Nombre estudiante', 'RUT', 'Asignatura', 'Nota1', 'Nota2', 'Nota3', 'Nota4', 'Nota5', 'Nota6', 'x̄'], $sep);

// Obtener todas las notas de todos los estudiantes del curso
$sql = $conexion->prepare("
    SELECT e.nombre AS estudiante, e.rut, a.nombre AS asignatura,
           n.nota1, n.nota2, n.nota3, n.nota4, n.nota5, n.nota6, n.x̄
    FROM notas n
    JOIN estudiantes e ON n.estudiante_id = e.id
    JOIN asignaturas a ON n.asignatura_id = a.id
    WHERE e.curso_id = ?
    ORDER BY e.nombre, a.nombre
");
$sql->bind_param("i", $curso_id);
$sql->execute();
$resultado = $sql->get_result();

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, [
        $fila['estudiante'],
        $fila['rut'],
        $fila['asignatura'],
        $fila['nota1'],
        $fila['nota2'],
        $fila['nota3'],
        $fila['nota4'],
        $fila['nota5'],
        $fila['nota6'],
        $fila['x̄']
    ], $sep);
}

fclose($output);
exit;
