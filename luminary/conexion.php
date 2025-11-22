<?php
$conexion = new mysqli("localhost", "root", "", "luminary");

if ($conexion->connect_error) {
    die("❌ Error al conectar con la base de datos: " . $conexion->connect_error);
}

// Opcional: establecer charset a utf8
$conexion->set_charset("utf8");
?>
