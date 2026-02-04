<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';

header('Content-Type: application/json');

echo json_encode([
    "id" => $_SESSION['estudiante_id'],
    "nombre" => $_SESSION['estudiante_nombre'],
    "apellidos" => $_SESSION['estudiante_apellidos'],
    "rut" => $_SESSION['estudiante_rut'],
    "curso_id" => $_SESSION['curso_id'],
    "curso_nivel" => $_SESSION['curso_nivel'],
    "curso_letra" => $_SESSION['curso_letra'],
    "curso" => $_SESSION['curso']
]);
