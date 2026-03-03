<?php
session_start();
header("Content-Type: application/json");

define("SESSION_TIMEOUT", 30 * 60);

// 🔐 No logueado
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'editor') {
    http_response_code(401);
    echo json_encode([
        "error" => "No autorizado"
    ]);
    exit;
}

// ⏳ Expiración
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();

        http_response_code(401);
        echo json_encode([
            "error" => "Sesión expirada"
        ]);
        exit;
    }
}

// 🔄 Renovar actividad
$_SESSION['last_activity'] = time();
