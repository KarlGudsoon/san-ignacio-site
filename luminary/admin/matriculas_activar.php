<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    exit("No autorizado");
}

if (!isset($_GET['id'])) {
    exit("ID no recibido");
}

$id = intval($_GET['id']);

// Obtener los datos de la matrícula
$sql = $conexion->query("SELECT * FROM matriculas WHERE id = $id LIMIT 1");

if ($sql->num_rows === 0) {
    exit("Matrícula no encontrada");
}

$matricula = $sql->fetch_assoc();
$curso_id = $matricula['curso_preferido'];

// Insertar al estudiante en la tabla estudiantes
$insert = $conexion->query("
    INSERT INTO estudiantes (matricula_id, curso_id)
    VALUES ($id, $curso_id)
");

if (!$insert) {
    exit("Error al insertar estudiante: " . $conexion->error);
}

// 🔥 OBTENER EL ID DEL ESTUDIANTE CREADO
$estudianteNuevoID = $conexion->insert_id;

// 🔥 ACTUALIZAR matriculas.estudiante_id
$updateEstID = $conexion->query("
    UPDATE matriculas 
    SET estudiante_id = $estudianteNuevoID
    WHERE id = $id
");

if (!$updateEstID) {
    exit("Error al asignar estudiante_id: " . $conexion->error);
}

// Actualizar estado de la matrícula
$update = $conexion->query("
    UPDATE matriculas SET estado = 'Activa' WHERE id = $id
");

header("Location: matriculas.php?mensaje=activada");
exit;
