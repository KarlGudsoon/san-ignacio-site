<?php
session_start();
require_once '../conexion.php';

// Verificar rol administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    exit("No autorizado");
}

// Verificar que llegue el ID
if (!isset($_POST['id'])) {
    exit("ID no recibido");
}

$id = intval($_POST['id']);

// Preparar eliminación
$stmt = $conexion->prepare("DELETE FROM matriculas WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "ERROR: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
