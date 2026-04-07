<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";

header("Content-Type: application/json");

$curso_profesor_id = intval($_POST['curso_profesor_id']);
$nombre = trim($_POST['unidad_nombre']);
$docente_id = $_SESSION['user_id'];

/* Validar que el curso pertenece al docente */
$stmt = $conexion->prepare("
    SELECT id FROM curso_profesor 
    WHERE id = ? AND profesor_id = ?
");
$stmt->bind_param("ii", $curso_profesor_id, $docente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

/* Verificar que no exista otra unidad con el mismo nombre */
$stmt = $conexion->prepare("
    SELECT id FROM unidad 
    WHERE curso_profesor_id = ? 
    AND LOWER(nombre) = LOWER(?)
    AND activo = 1
");
$stmt->bind_param("is", $curso_profesor_id, $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Ya existe una unidad con ese nombre"
    ]);
    exit;
}

/* Insertar unidad */
$stmt = $conexion->prepare("
    INSERT INTO unidad (curso_profesor_id, nombre)
    VALUES (?, ?)
");

$stmt->bind_param("is", $curso_profesor_id, $nombre);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Unidad creada correctamente"
]);