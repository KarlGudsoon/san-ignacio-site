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

    // 🔴 Si no hay profesor seleccionado → eliminar asignación
    if ($nuevo_profesor_id <= 0) {

        if ($asignacion_existe) {

            $stmt = $conexion->prepare("
                DELETE FROM curso_profesor
                WHERE curso_id = ? AND asignatura_id = ?
            ");
            $stmt->bind_param("ii", $curso_id, $asignatura_id);
            $stmt->execute();
            $stmt->close();

        }

        continue;
    }

    // 🟡 Si existe pero cambió el profesor → actualizar
    if ($asignacion_existe) {

        if ($profesor_anterior_id != $nuevo_profesor_id) {

            $stmt = $conexion->prepare("
                UPDATE curso_profesor
                SET profesor_id = ?
                WHERE curso_id = ? AND asignatura_id = ?
            ");
            $stmt->bind_param("iii", $nuevo_profesor_id, $curso_id, $asignatura_id);
            $stmt->execute();
            $stmt->close();

            // actualizar también las notas
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

        // 🟢 No existía → insertar
        $stmt = $conexion->prepare("
            INSERT INTO curso_profesor (curso_id, asignatura_id, profesor_id)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $curso_id, $asignatura_id, $nuevo_profesor_id);
        $stmt->execute();
        $stmt->close();
    }
}

$_SESSION['mensaje'] = "Profesores actualizados correctamente.";
header("Location: ver_curso.php?id=$curso_id");
exit;
?>