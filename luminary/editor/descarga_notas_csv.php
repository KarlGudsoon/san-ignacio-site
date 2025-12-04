<?php
include '../conexion.php';
session_start();

if (!in_array($_SESSION['rol'], ['admin', 'editor'])) {
    header("Location: ../index.html");
    exit;
}

$estudiante_id = $_GET['id'] ?? null;
if (!$estudiante_id) {
    die("ID de estudiante no proporcionado.");
}

// Obtener datos del estudiante
$estudiante_sql = $conexion->prepare("SELECT m.nombre_estudiante, m.apellidos_estudiante, m.rut_estudiante, curso_id FROM estudiantes e INNER JOIN matriculas m ON e.matricula_id = m.id WHERE e.id = ?");
$estudiante_sql->bind_param("i", $estudiante_id);
$estudiante_sql->execute();
$estudiante = $estudiante_sql->get_result()->fetch_assoc();

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

$curso_id = $estudiante['curso_id'];
$nombre_archivo = 'notas_' . preg_replace('/\s+/', '_', strtolower($estudiante['nombre_estudiante']. '_' . $estudiante['apellidos_estudiante'])) . '.csv';

// Encabezados para descarga
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$nombre_archivo");

// Abrir salida
$output = fopen('php://output', 'w');
$separador = ';';

// Encabezados de columnas
fputcsv($output, ['Asignatura', 'Nota1', 'Nota2', 'Nota3', 'Nota4', 'Nota5', 'Nota6', 'Nota7', 'Nota8', 'Nota9', 'x̄'], $separador);

// Obtener asignaturas del curso y notas del estudiante
$sql = $conexion->prepare("
    SELECT a.nombre AS asignatura,
           n.nota1, n.nota2, n.nota3, n.nota4, n.nota5, n.nota6, n.nota7, n.nota8, n.nota9, n.x̄
    FROM curso_asignatura ca
    INNER JOIN asignaturas a ON ca.asignatura_id = a.id
    LEFT JOIN notas n ON a.id = n.asignatura_id AND n.estudiante_id = ?
    WHERE ca.curso_id = ?
    ORDER BY a.nombre
");
$sql->bind_param("ii", $estudiante_id, $curso_id);
$sql->execute();
$resultado = $sql->get_result();

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, [
        $fila['asignatura'],
        $fila['nota1'] ?? '',
        $fila['nota2'] ?? '',
        $fila['nota3'] ?? '',
        $fila['nota4'] ?? '',
        $fila['nota5'] ?? '',
        $fila['nota6'] ?? '',
        $fila['nota7'] ?? '',
        $fila['nota8'] ?? '',
        $fila['nota9'] ?? '',
        $fila['x̄'] ?? ''
    ], $separador);
}

fclose($output);
exit;
