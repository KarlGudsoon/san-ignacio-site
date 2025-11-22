<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit;
}
echo "¡Bienvenido, " . $_SESSION['nombre'] . "!";
?>

<a href="logout.php">Cerrar sesión</a>
