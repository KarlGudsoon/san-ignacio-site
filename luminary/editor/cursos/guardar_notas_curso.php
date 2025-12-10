<?php
include '../../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

$curso_id = $_POST['curso_id'];
$asignatura_id = $_POST['asignatura_id'];
$profesor_id = $_SESSION['id'];
$notas_post = $_POST['notas'];

foreach ($notas_post as $estudiante_id => $notas_array) {
    // Preparar valores de notas (si el campo está vacío, se usa NULL)
    $valores = [];
    for ($i = 1; $i <= 9; $i++) {
        $campo = "nota$i";
        $nota = trim($notas_array[$campo]);
        $valores[$campo] = ($nota === '') ? "NULL" : floatval($nota);
    }

    // Revisar si ya existe una fila para ese estudiante, asignatura y profesor
    $existe = $conexion->query("
        SELECT id FROM notas
        WHERE estudiante_id = $estudiante_id AND asignatura_id = $asignatura_id AND profesor_id = $profesor_id
    ");

    if ($existe->num_rows > 0) {
        // Update
        $conexion->query("
            UPDATE notas SET
                nota1 = {$valores['nota1']},
                nota2 = {$valores['nota2']},
                nota3 = {$valores['nota3']},
                nota4 = {$valores['nota4']},
                nota5 = {$valores['nota5']},
                nota6 = {$valores['nota6']},
                nota7 = {$valores['nota7']},
                nota8 = {$valores['nota8']},
                nota9 = {$valores['nota9']}
            WHERE estudiante_id = $estudiante_id AND asignatura_id = $asignatura_id AND profesor_id = $profesor_id
        ");
    } else {
        // Insert
        $conexion->query("
            INSERT INTO notas (
                estudiante_id, asignatura_id, profesor_id,
                nota1, nota2, nota3, nota4, nota5, nota6,
                nota7, nota8, nota9
            ) VALUES (
                $estudiante_id, $asignatura_id, $profesor_id,
                {$valores['nota1']}, {$valores['nota2']}, {$valores['nota3']},
                {$valores['nota4']}, {$valores['nota5']}, {$valores['nota6']},
                {$valores['nota7']}, {$valores['nota8']}, {$valores['nota9']}
            )
        ");
    }
}

$_SESSION['mensaje_exito'] = "✅ Notas guardadas correctamente.";
header("Location: curso.php?curso_id=$curso_id&asignatura_id=$asignatura_id");
exit;

