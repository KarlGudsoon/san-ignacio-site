<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

$curso_id = $_POST['curso_id'];
$asignaciones = $_POST['profesores'] ?? [];

if (!is_numeric($curso_id)) {
    $_SESSION['error'] = "Curso no válido.";
    header("Location: admin_cursos.php");
    exit;
}

foreach ($asignaciones as $asignatura_id => $nuevo_profesor_id) {
    $asignatura_id = (int)$asignatura_id;
    $nuevo_profesor_id = (int)$nuevo_profesor_id;

    if ($nuevo_profesor_id <= 0) continue;

    // Verificar si ya existe una asignación previa
    $stmt = $conexion->prepare("
        SELECT profesor_id FROM curso_profesor
        WHERE curso_id = ? AND asignatura_id = ?
    ");
    $stmt->bind_param("ii", $curso_id, $asignatura_id);
    $stmt->execute();
    $stmt->bind_result($profesor_anterior_id);
    $asignacion_existe = $stmt->fetch();
    $stmt->close();

    if ($asignacion_existe) {
        if ($profesor_anterior_id != $nuevo_profesor_id) {
            // Cambiar asignación
            $stmt = $conexion->prepare("
                UPDATE curso_profesor
                SET profesor_id = ?
                WHERE curso_id = ? AND asignatura_id = ?
            ");
            $stmt->bind_param("iii", $nuevo_profesor_id, $curso_id, $asignatura_id);
            $stmt->execute();
            $stmt->close();

            // También actualizar profesor_id en la tabla notas
            $stmt = $conexion->prepare("
                UPDATE notas
                SET profesor_id = ?
                WHERE asignatura_id = ?
                AND estudiante_id IN (
                    SELECT id FROM estudiantes WHERE curso_id = ?
                )
            ");
            $stmt->bind_param("iii", $nuevo_profesor_id, $asignatura_id, $curso_id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // No existía asignación: insertamos nueva
        $stmt = $conexion->prepare("
            INSERT INTO curso_profesor (curso_id, asignatura_id, profesor_id)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $curso_id, $asignatura_id, $nuevo_profesor_id);
        $stmt->execute();
        $stmt->close();

        // También actualizar profesor_id en las notas existentes (si las hay)
        $stmt = $conexion->prepare("
            UPDATE notas
            SET profesor_id = ?
            WHERE asignatura_id = ?
            AND estudiante_id IN (
                SELECT id FROM estudiantes WHERE curso_id = ?
            )
        ");
        $stmt->bind_param("iii", $nuevo_profesor_id, $asignatura_id, $curso_id);
        $stmt->execute();
        $stmt->close();
    }
}

$_SESSION['mensaje'] = "Profesores asignados y notas actualizadas correctamente.";
header("Location: ver_curso.php?id=$curso_id");
exit;
?>
