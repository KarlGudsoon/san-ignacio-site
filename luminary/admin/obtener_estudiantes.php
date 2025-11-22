<?php
include '../conexion.php';

header('Content-Type: application/json');

$curso_id = $_GET['curso_id'] ?? 0;
$curso_id = (int)$curso_id;

$resultado = [];

if ($curso_id > 0) {
    $stmt = $conexion->prepare("SELECT id, nombre FROM estudiantes WHERE curso_id = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $resultado[] = $fila;
    }
    $stmt->close();
}

echo json_encode($resultado);
