<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../middlewares/auth_editor.php";

/* ==============================
   VALIDAR DATOS BÁSICOS
============================== */

$curso_profesor_id = intval($_POST['material_curso_profesor_id'] ?? 0);
$titulo = trim($_POST['material_titulo'] ?? '');
$descripcion = trim($_POST['material_descripcion'] ?? '');
$unidad_id = intval($_POST['material_unidad_id'] ?? 0);
$categoria_id = intval($_POST['material_categoria_id'] ?? 0);
$tipo_envio = $_POST['tipo'] ?? null;

$docente_id = $_SESSION['user_id'] ?? 0;

if (!$curso_profesor_id || !$unidad_id || !$categoria_id || empty($titulo) || !$tipo_envio) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

/* ==============================
   VALIDAR RELACIÓN DOCENTE-CURSO
============================== */

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

/* ==============================
   VALIDAR CATEGORÍA ACTIVA
============================== */

$stmt = $conexion->prepare("
    SELECT id FROM material_categoria 
    WHERE id = ? AND activo = 1
");
$stmt->bind_param("i", $categoria_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Categoría inválida"]);
    exit;
}

/* ==============================
   PROCESAR SEGÚN TIPO
============================== */

$nombreFinal = null;
$tipo = null;

if ($tipo_envio === "archivo") {

    if (!isset($_FILES['material_archivo'])) {
        echo json_encode(["success" => false, "message" => "No se envió archivo"]);
        exit;
    }

    $archivo = $_FILES['material_archivo'];
    $permitidos = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png'];
    $extensionReal = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensionReal, $permitidos)) {
        echo json_encode(["success" => false, "message" => "Tipo de archivo no permitido"]);
        exit;
    }

    /* Detectar tipo interno */
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
            $tipo = 'archivo';
    }

    /* Generar nombre único */
    $nombreFinal = uniqid("mat_") . "." . $extensionReal;
    $rutaDestino = __DIR__ . "/../../../uploads/material/" . $nombreFinal;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo json_encode(["success" => false, "message" => "Error subiendo archivo"]);
        exit;
    }

} elseif ($tipo_envio === "enlace") {

    /* Es enlace o video */
    $enlace = trim($_POST['archivo'] ?? '');

    if (empty($enlace)) {
        echo json_encode(["success" => false, "message" => "Debe ingresar un enlace"]);
        exit;
    }

    if (!filter_var($enlace, FILTER_VALIDATE_URL)) {
        echo json_encode(["success" => false, "message" => "Enlace no válido"]);
        exit;
    }

    $nombreFinal = $enlace;

    /* Detectar si es YouTube */
    if (
        stripos($enlace, "youtube.com") !== false ||
        stripos($enlace, "youtu.be") !== false
    ) {
        $tipo = "video";
    } else {
        $tipo = "enlace";
    }
}

/* ==============================
   INSERTAR EN BASE DE DATOS
============================== */

$stmt = $conexion->prepare("
    INSERT INTO material 
    (curso_profesor_id, categoria_id, unidad_id, titulo, descripcion, archivo, tipo)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iiissss",
    $curso_profesor_id,
    $categoria_id,
    $unidad_id,
    $titulo,
    $descripcion,
    $nombreFinal,
    $tipo
);

$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Material subido correctamente"
]);