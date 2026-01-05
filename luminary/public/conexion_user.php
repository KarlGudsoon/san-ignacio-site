<?php
$config = require '/home2/sanignac/config/db_user.php';

$conexion = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['db']
);

$conexion->set_charset($config['charset']);

if ($conexion->connect_error) {
    error_log("Error BD: " . $conexion->connect_error);
    die("Error de conexión. Intente más tarde.");
}
?>