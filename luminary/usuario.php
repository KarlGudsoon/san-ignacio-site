<?php
session_start();
if ($_SESSION['rol'] !== 'usuario') {
    header("Location: index.html");
    exit;
}
echo "Bienvenido USUARIO: " . $_SESSION['nombre'];
?>

<a href="logout.php">Cerrar sesión</a>
