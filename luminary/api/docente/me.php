<?php
require_once __DIR__ . '/../middlewares/auth_editor.php';

header('Content-Type: application/json');

echo json_encode([
    "id" => $_SESSION['user_id'],
    "nombre" => $_SESSION['user_nombre'],
    "correo" => $_SESSION['user_correo'],
]);
