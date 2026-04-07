<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../../config/db.php";

if (!isset($_POST['material_id'])) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$material_id = intval($_POST['material_id']);

/* 1️⃣ Verificar que el material */
$stmt = $conexion->prepare("
    SELECT m.archivo
    FROM material_distancia m
    WHERE m.id = ?
");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No se encontró el material"]);
    exit;
}

$material = $result->fetch_assoc();
$rutaArchivo = __DIR__ . "/../../../../uploads/material_distancia/" . $material['archivo'];

/* 2️⃣ Eliminar archivo físico */
if (file_exists($rutaArchivo)) {
    unlink($rutaArchivo);
}

/* 3️⃣ Eliminar registro BD */
$stmt = $conexion->prepare("DELETE FROM material_distancia WHERE id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Material eliminado correctamente"
]);