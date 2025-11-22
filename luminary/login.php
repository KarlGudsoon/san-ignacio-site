<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    if (empty($correo) || empty($contrasena)) {
        echo "Por favor completa todos los campos.";
        exit;
    }

    // Buscar el usuario por correo
    $sql = "SELECT id, nombre, contrasena, correo, rol FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Guardar datos en sesión
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['correo'] = $usuario['correo'];

            // Redirigir según el rol
            switch ($usuario['rol']) {
                case 'admin':
                    header("Location: admin/admin.php");
                    break;
                case 'editor':
                    header("Location: editor/editor.php");
                    break;
                case 'usuario':
                    header("Location: usuario.php");
                    break;
                default:
                    echo "Rol no válido.";
            }
            exit;
        } else {
            header("Location: index.html?error=contrasena_incorrecta");
            exit;
        }
    } else {
        header("Location: index.html?error=correo_no_encontrado");
            exit;
    }
}
?>
