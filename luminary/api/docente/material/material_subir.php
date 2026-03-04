<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_editor.php';

if (!isset($_FILES['material_archivo'])) {
    echo json_encode(["success" => false, "message" => "No se envió archivo"]);
    exit;
}

$curso_profesor_id = intval($_POST['material_curso_profesor_id']);
$titulo = trim($_POST['material_titulo']);
$descripcion = trim($_POST['material_descripcion']);
$unidad_id = intval($_POST['material_unidad_id']);
$categoria_id = intval($_POST['material_categoria_id']);
$docente_id = $_SESSION['user_id'];

/* Validar relación docente-curso */
$stmt = $conexion->prepare("
    SELECT id FROM curso_profesor 
    WHERE id = ? AND profesor_id = ?
");
$stmt->bind_param("ii", $curso_profesor_id, $docente_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

/* Validar archivo */
$archivo = $_FILES['material_archivo'];
$permitidos = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png'];

$extensionReal = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

switch ($extensionReal) {
    case 'pdf':
        $tipo = 'pdf';
        break;

    case 'doc':
    case 'docx':
        $tipo = 'doc';
        break;

    case 'ppt':
    case 'pptx':
        $tipo = 'ppt';
        break;

    case 'jpg':
    case 'jpeg':
    case 'png':
        $tipo = 'imagen';
        break;

    default:
        $tipo = 'otro';
}

if (!in_array($extensionReal, $permitidos)) {
    echo json_encode(["success" => false, "message" => "Tipo de archivo no permitido"]);
    exit;
}

/* Generar nombre único */
$nombreNuevo = uniqid("mat_") . "." . $extensionReal;
$rutaDestino = __DIR__ . "/../../../uploads/material/" . $nombreNuevo;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    echo json_encode(["success" => false, "message" => "Error subiendo archivo"]);
    exit;
}

$stmt = $conexion->prepare("SELECT id FROM material_categoria WHERE id = ? AND activo = 1");
$stmt->bind_param("i", $categoria_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Categoría inválida"]);
    exit;
}

/* Guardar en BD */
$stmt = $conexion->prepare("
    INSERT INTO material 
    (curso_profesor_id, categoria_id, unidad_id, titulo, descripcion, archivo, tipo)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("iiissss",
    $curso_profesor_id,
    $categoria_id,
    $unidad_id,
    $titulo,
    $descripcion,
    $nombreNuevo,
    $tipo
);

$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Material subido correctamente"
]);