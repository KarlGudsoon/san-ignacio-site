<?php
include '../conexion.php';
session_start();

// Validar sesión y rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

// Validar profesor_id
if (!isset($_SESSION['id'])) {
    die("Error: Sesión no válida.");
}

$curso_id = $_GET['curso_id'] ?? null;
$profesor_id = $_SESSION['id'];

if (!$curso_id || !is_numeric($curso_id)) {
    die("Error: ID de curso no válido.");
}

// Verificar que el profesor es jefe del curso
$verificacion = $conexion->prepare("SELECT nivel, letra FROM cursos WHERE id = ? AND profesor_jefe_id = ?");
$verificacion->bind_param("ii", $curso_id, $profesor_id);
$verificacion->execute();
$resultado_verif = $verificacion->get_result();

if ($resultado_verif->num_rows === 0) {
    die("Error: No tiene permiso para descargar las notas de este curso.");
}

$curso = $resultado_verif->fetch_assoc();
$nombre_archivo = "notas_curso_{$curso['nivel']}{$curso['letra']}_" . date('Y-m-d') . ".csv";

// Encabezados para descarga
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
header('Pragma: no-cache');
header('Expires: 0');

// Abrir la salida
$output = fopen('php://output', 'w');

// BOM para UTF-8 en Excel
fwrite($output, "\xEF\xBB\xBF");

// Separador (Excel en español usa ;)
$separador = ';';

// Encabezados completos (incluyendo todas las notas posibles)
$encabezados = ['Nombre estudiante', 'RUT'];
for ($i = 1; $i <= 9; $i++) {
    $encabezados[] = "Nota$i";
}
$encabezados[] = 'Suma (Σ)';
$encabezados[] = 'Promedio (x̄)';
$encabezados[] = 'Asignatura';

fputcsv($output, $encabezados, $separador);

// Obtener todas las notas de todos los estudiantes del curso
$sql = $conexion->prepare("
    SELECT 
        TRIM(CONCAT(COALESCE(m.nombre_estudiante, ''), ' ', COALESCE(m.apellidos_estudiante, ''))) AS estudiante,
        COALESCE(m.rut_estudiante, 'N/A') AS rut,
        a.nombre AS asignatura,
        COALESCE(n.nota1, '') AS nota1,
        COALESCE(n.nota2, '') AS nota2,
        COALESCE(n.nota3, '') AS nota3,
        COALESCE(n.nota4, '') AS nota4,
        COALESCE(n.nota5, '') AS nota5,
        COALESCE(n.nota6, '') AS nota6,
        COALESCE(n.nota7, '') AS nota7,
        COALESCE(n.nota8, '') AS nota8,
        COALESCE(n.nota9, '') AS nota9,
        COALESCE(n.Σ, '') AS suma,
        COALESCE(n.x̄, '') AS promedio
    FROM estudiantes e
    LEFT JOIN matriculas m ON e.matricula_id = m.id
    LEFT JOIN notas n ON e.id = n.estudiante_id
    LEFT JOIN asignaturas a ON n.asignatura_id = a.id
    WHERE e.curso_id = ?
    ORDER BY m.apellidos_estudiante, m.nombre_estudiante, a.nombre
");
$sql->bind_param("i", $curso_id);
$sql->execute();
$resultado = $sql->get_result();

if ($resultado->num_rows === 0) {
    // Escribir mensaje si no hay datos
    fputcsv($output, ['No hay notas registradas para este curso'], $separador);
} else {
    while ($fila = $resultado->fetch_assoc()) {
        // Preparar fila con todos los datos
        $fila_csv = [
            $fila['estudiante'],
            $fila['rut']
        ];
        
        // Agregar todas las notas
        for ($i = 1; $i <= 9; $i++) {
            $fila_csv[] = $fila["nota$i"] !== '' ? str_replace('.', ',', $fila["nota$i"]) : '';
        }
        
        // Agregar suma y promedio (formatear decimales para Excel español)
        $fila_csv[] = $fila['suma'] !== '' ? str_replace('.', ',', $fila['suma']) : '';
        $fila_csv[] = $fila['promedio'] !== '' ? str_replace('.', ',', $fila['promedio']) : '';
        $fila_csv[] = $fila['asignatura'] ?? 'Sin asignatura';
        
        fputcsv($output, $fila_csv, $separador);
    }
}

fclose($output);
exit;
?>