<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

$nombre = $_POST['nombre'] ?? '';
$rut = $_POST['rut'] ?? '';
$curso_id = $_POST['curso_id'] ?? null;

if (!$nombre || !$rut || !$curso_id) {
    $_SESSION['error'] = "Datos incompletos.";
    header("Location: ver_curso.php?id=$curso_id");
    exit;
}

try {
    $conexion->begin_transaction();

    // Insertar estudiante
    $stmt = $conexion->prepare("INSERT INTO estudiantes (nombre, rut, curso_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nombre, $rut, $curso_id);
    $stmt->execute();
    $estudiante_id = $stmt->insert_id;
    $stmt->close();

    // Obtener asignaturas habilitadas para el curso
    $stmt = $conexion->prepare("SELECT asignatura_id FROM curso_asignatura WHERE curso_id = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();

    // Obtener asignaturas y profesor_id del curso
    $stmt = $conexion->prepare("SELECT asignatura_id, profesor_id FROM curso_profesor WHERE curso_id = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();

    // Insertar en notas: estudiante_id, asignatura_id, profesor_id
    $stmt = $conexion->prepare("INSERT INTO notas (estudiante_id, asignatura_id, profesor_id) VALUES (?, ?, ?)");
    while ($row = $resultado->fetch_assoc()) {
        $asignatura_id = $row['asignatura_id'];
        $profesor_id = $row['profesor_id'];
        $stmt->bind_param("iii", $estudiante_id, $asignatura_id, $profesor_id);
        $stmt->execute();
    }
    $stmt->close();

    // Insertar filas vacías en notas
    $stmt = $conexion->prepare("INSERT INTO notas (estudiante_id, asignatura_id) VALUES (?, ?)");
    while ($row = $resultado->fetch_assoc()) {
        $asignatura_id = $row['asignatura_id'];
        $stmt->bind_param("ii", $estudiante_id, $asignatura_id);
        $stmt->execute();
    }
    $stmt->close();

    $conexion->commit();
    $_SESSION['mensaje'] = "Estudiante agregado correctamente.";
    header("Location: ver_curso.php?id=$curso_id");
    exit;

} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['error'] = "Error al agregar estudiante: " . $e->getMessage();
    header("Location: ver_curso.php?id=$curso_id");
    exit;
}
