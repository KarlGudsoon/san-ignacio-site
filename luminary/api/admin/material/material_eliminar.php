<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../middlewares/auth_editor.php";

if (!isset($_POST['material_id'])) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$material_id = intval($_POST['material_id']);
$docente_id = $_SESSION['user_id'];

/* 1️⃣ Verificar que el material pertenece al docente */
$stmt = $conexion->prepare("
    SELECT m.archivo
    FROM material m
    JOIN curso_profesor cp ON m.curso_profesor_id = cp.id
    WHERE m.id = ? AND cp.profesor_id = ?
");
$stmt->bind_param("ii", $material_id, $docente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$material = $result->fetch_assoc();
$rutaArchivo = __DIR__ . "/../../../uploads/material/" . $material['archivo'];

/* 2️⃣ Eliminar archivo físico */
if (file_exists($rutaArchivo)) {
    unlink($rutaArchivo);
}

/* 3️⃣ Eliminar registro BD */
$stmt = $conexion->prepare("DELETE FROM material WHERE id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Material eliminado correctamente"
]);