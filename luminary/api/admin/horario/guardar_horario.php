<?php
require_once __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$curso_id = $data["curso_id"];
$dia = $data["dia"];
$bloque_id = $data["bloque_id"];
$asignatura_id = $data["asignatura_id"] ?? null;

// 🔎 Verificar si ya existe registro
$stmt = $conexion->prepare("
    SELECT id FROM horarios
    WHERE curso_id = ? AND dia = ? AND bloque_id = ?
");
$stmt->bind_param("isi", $curso_id, $dia, $bloque_id);
$stmt->execute();
$result = $stmt->get_result();


// 🔴 SI ES NULL → ELIMINAR
if ($asignatura_id === null) {

    $stmt = $conexion->prepare("
        DELETE FROM horarios
        WHERE curso_id = ? AND dia = ? AND bloque_id = ?
    ");
    $stmt->bind_param("isi", $curso_id, $dia, $bloque_id);
    $stmt->execute();

    echo json_encode(["success" => true]);
    exit;
}


// 🟢 SI EXISTE → UPDATE
if ($result->num_rows > 0) {

    $stmt = $conexion->prepare("
        UPDATE horarios
        SET asignatura_id = ?
        WHERE curso_id = ? AND dia = ? AND bloque_id = ?
    ");
    $stmt->bind_param("iisi", $asignatura_id, $curso_id, $dia, $bloque_id);
    $stmt->execute();

} else {

    // 🟢 SI NO EXISTE → INSERT
    $stmt = $conexion->prepare("
        INSERT INTO horarios (curso_id, dia, bloque_id, asignatura_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isii", $curso_id, $dia, $bloque_id, $asignatura_id);
    $stmt->execute();
}

echo json_encode(["success" => true]);