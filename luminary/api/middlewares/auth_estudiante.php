<?php
session_start();

define("SESSION_TIMEOUT", 30 * 60);

// 1️⃣ Verificar login
if (!isset($_SESSION['estudiante_id'])) {
    header("Location: /luminary/");
    exit;
}

// 2️⃣ Expiración por inactividad
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header("Location: /luminary/?expired=1");
        exit;
    }
}

// 3️⃣ Renovar actividad
$_SESSION['last_activity'] = time();
