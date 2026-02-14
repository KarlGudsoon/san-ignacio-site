<?php
session_start();
require_once __DIR__ . "/../../config/db.php";

// 1️⃣ Obtener datos
$email = trim($_POST['correo'] ?? '');
$password = $_POST['contrasena'] ?? '';

if (!$email || !$password) {
    header("Location: /luminary/login?error=1");
    exit;
}

// 2️⃣ Buscar usuario
$sql = "SELECT id, nombre, correo, contrasena, rol 
        FROM usuarios 
        WHERE correo = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: /luminary/login?error=1");
    exit;
}

// 3️⃣ Verificar contraseña
if (!password_verify($password, $usuario['contrasena'])) {
    header("Location: /luminary/login?error=1");
    exit;
}

// 4️⃣ Crear sesión segura
session_regenerate_id(true);

$_SESSION['user_id'] = $usuario['id'];
$_SESSION['user_nombre'] = $usuario['nombre'];
$_SESSION['user_correo'] = $usuario['correo'];
$_SESSION['user_rol'] = $usuario['rol'];
$_SESSION['last_activity'] = time();

// 5️⃣ Redirigir según rol
switch ($usuario['rol']) {

    case 'admin':
        $redirect = "/luminary/admin/dashboard";
        break;

    case 'editor':
        $redirect = "/luminary/docente/dashboard";
        break;

    default:
        // Rol no permitido
        session_destroy();
        header("Location: /luminary/login?error=2");
        exit;
}

header("Location: $redirect");
exit;
