<?php
session_start();

// Asegúrate de que solo los administradores puedan acceder a esta página
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

require_once '../conexion.php'; // Asegúrate de que esta ruta sea correcta

$mensaje = ""; // Variable para guardar mensajes de éxito o error

// --- 2. Lógica de Procesamiento del Formulario (se ejecuta al enviar el formulario) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y Sanitizar los Datos
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena_plana = $_POST['contrasena'];
    $rol = 'admin'; // El rol siempre será 'admin'
    $asignatura = NULL; // La asignatura es NULL para los administradores

    // 2. Validación Básica
    if (empty($nombre) || empty($correo) || empty($contrasena_plana)) {
        $mensaje = "<p style='color: red;'>Por favor, complete todos los campos requeridos.</p>";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<p style='color: red;'>El formato del correo electrónico no es válido.</p>";
    } else {
        // 3. Hash de la Contraseña (¡Muy Importante!)
        // Usa PASSWORD_DEFAULT para el algoritmo de hash más actual y seguro
        $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        
        // 4. Preparar la Consulta SQL
        // Usamos consultas preparadas para prevenir ataques de inyección SQL
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, fecha_registro, rol, asignatura) VALUES (?, ?, ?, NOW(), ?, ?)";
        
        // Prepara la declaración
        if ($stmt = $conexion->prepare($sql)) {
            // Vincula los parámetros
            // 's' indica que el parámetro es de tipo string
            $stmt->bind_param("sssss", $nombre, $correo, $contrasena_hash, $rol, $asignatura);
            
            // 5. Ejecutar la Consulta
            if ($stmt->execute()) {
                $mensaje = "<p style='color: green;'>✅ ¡Administrador **{$nombre}** registrado con éxito!</p>";
            } else {
                // Error en la ejecución (ej. correo duplicado)
                // Usamos $conexion->error para obtener el mensaje de error de la BD
                $mensaje = "<p style='color: red;'>❌ Error al registrar: " . $stmt->error . "</p>";
            }

            // Cierra la declaración
            $stmt->close();
        } else {
            $mensaje = "<p style='color: red;'>❌ Error de preparación de la consulta: " . $conexion->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Directivo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/global.css">   
    <link rel="stylesheet" href="style.css">   

    <style>
        input {
            width: auto;
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: Outfit, sans-serif;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        section {
            display: flex;
            height: fit-content;
            padding: 1rem;
            color: #eee;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0 0 0 / 35%);
            background-color: var(--secondarycolor);
        }

        section form {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            gap: 1rem;
            width: 100%;
            flex-wrap: wrap;
        }

        .asignatura {
            color: white;
            border-radius: 0.5rem;
        }
        
    </style>
</head>
<body>
    <?php
    include "components/aside.php"
    ?>
    <main>
        <div class="contenedor-informacion"> 
            <h1>Configuración</h1>
            <p>Aquí podrás configurar diferentes apartados del sistema Luminary</p>
        </div>
        <section>
            <h2 style="text-align: center;">Registrar Nuevo Administrador</h2>
            <?php echo $mensaje; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div>
                    
                </div>
                <div>
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                    <input type="submit" value="Registrar Administrador">
                </div>
                
            </form>
        </section>
        
    </main>
    <?php
    include "components/aside_bottom.php"
    ?>

    
</body>
</html>

<script>

let asignatura = document.querySelectorAll('.asignatura');

asignatura.forEach((element) => {
    if (element.innerHTML === "Ciencias") {
        element.style.backgroundColor = "#0da761";
    } else if (element.innerHTML === "Matemáticas") {
        element.style.backgroundColor = "#3891e9"; 
    } else if (element.innerHTML === "Lenguaje") {
        element.style.backgroundColor = "#f75353"; 
    } else if (element.innerHTML === "Estudios Sociales") {
        element.style.backgroundColor = "#ed861f"; 
    } else if (element.innerHTML = "Inglés") {
        element.style.backgroundColor = "#cdb51a"; 
    } else if (element.innerHTML === "Inglés Comunicativo") {
        element.style.backgroundColor = "#23babf"; 
    } else if (element.innerHTML === "TICs") {
        element.style.backgroundColor = "#8544cf"; 
    }
});
</script>