<?php
session_start();

// Asegúrate de que solo los administradores puedan acceder a esta página
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

require_once '../conexion.php'; // Asegúrate de que esta ruta sea correcta

// Variables para el Toast: contendrán el mensaje y su tipo (success, error, warning)
$mensaje_texto = "";
$mensaje_tipo = ""; 

// -----------------------------------------------------
// --- 1. Lógica para REGISTRAR un nuevo Administrador ---
// -----------------------------------------------------
// Se activa si se envían los campos de registro (nombre, correo, contrasena)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre']) && !isset($_POST['eliminar_id'])) {
    // 1. Recoger y Sanitizar los Datos
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena_plana = $_POST['contrasena'];
    $rol = 'admin'; // El rol siempre será 'admin'
    $asignatura = NULL; // La asignatura es NULL para los administradores

    // 2. Validación Básica
    if (empty($nombre) || empty($correo) || empty($contrasena_plana)) {
        $mensaje_texto = "Por favor, complete todos los campos requeridos para el registro.";
        $mensaje_tipo = "warning";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_texto = "El formato del correo electrónico no es válido.";
        $mensaje_tipo = "warning";
    } else {
        // 3. Hash de la Contraseña (¡Muy Importante!)
        $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        
        // 4. Preparar la Consulta SQL
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, fecha_registro, rol, asignatura) VALUES (?, ?, ?, NOW(), ?, ?)";
        
        // Prepara la declaración
        if ($stmt = $conexion->prepare($sql)) {
            // Vincula los parámetros
            $stmt->bind_param("sssss", $nombre, $correo, $contrasena_hash, $rol, $asignatura);
            
            // 5. Ejecutar la Consulta
            if ($stmt->execute()) {
                $mensaje_texto = "✅ ¡Administrador " . htmlspecialchars($nombre) . " registrado con éxito!";
                $mensaje_tipo = "success";
            } else {
                // Error en la ejecución (ej. correo duplicado)
                $mensaje_texto = "❌ Error al registrar: " . $stmt->error;
                $mensaje_tipo = "error";
            }
            $stmt->close();
        } else {
            $mensaje_texto = "❌ Error de preparación de la consulta de registro: " . $conexion->error;
            $mensaje_tipo = "error";
        }
    }
}

// --------------------------------------------------
// --- 2. Lógica para ELIMINAR un Administrador ---
// --------------------------------------------------
// Se activa si se envía el ID de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_id'])) {
    $id_a_eliminar = $_POST['eliminar_id'];

    // Evitar que el administrador actualmente logueado se elimine a sí mismo
    if (isset($_SESSION['id']) && $id_a_eliminar == $_SESSION['id']) { 
        $mensaje_texto = "⚠️ No puedes eliminar tu propia cuenta de administrador mientras estás logueado.";
        $mensaje_tipo = "warning";
    } else {
        // Consulta preparada para la eliminación
        $sql_delete = "DELETE FROM usuarios WHERE id = ? AND rol = 'admin'";
        
        if ($stmt_delete = $conexion->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id_a_eliminar); // 'i' para integer
            
            if ($stmt_delete->execute()) {
                // Verificar si se eliminó alguna fila
                if ($stmt_delete->affected_rows > 0) {
                    $mensaje_texto = "✅ Administrador con ID " . htmlspecialchars($id_a_eliminar) . " eliminado con éxito.";
                    $mensaje_tipo = "success";
                } else {
                    $mensaje_texto = "❌ Error: No se encontró un administrador con ese ID para eliminar.";
                    $mensaje_tipo = "error";
                }
            } else {
                $mensaje_texto = "❌ Error al ejecutar la eliminación: " . $stmt_delete->error;
                $mensaje_tipo = "error";
            }
            $stmt_delete->close();
        } else {
            $mensaje_texto = "❌ Error de preparación de la consulta de eliminación: " . $conexion->error;
            $mensaje_tipo = "error";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cambiar_contrasena'])) {
    $id_usuario_logueado = $_SESSION['id'];
    $contrasena_actual = $_POST['contrasena_actual'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // 1. Validación
    if (empty($contrasena_actual) || empty($nueva_contrasena)) {
        $mensaje_texto = "Por favor, complete ambos campos para cambiar la contraseña.";
        $mensaje_tipo = "warning";
    } elseif (strlen($nueva_contrasena) < 6) { // Regla básica de seguridad
        $mensaje_texto = "La nueva contraseña debe tener al menos 6 caracteres.";
        $mensaje_tipo = "warning";
    } else {
        // 2. Obtener la contraseña actual hasheada de la base de datos
        $sql_fetch = "SELECT contrasena FROM usuarios WHERE id = ?";
        if ($stmt_fetch = $conexion->prepare($sql_fetch)) {
            $stmt_fetch->bind_param("i", $id_usuario_logueado);
            $stmt_fetch->execute();
            $result_fetch = $stmt_fetch->get_result();
            $usuario = $result_fetch->fetch_assoc();
            $stmt_fetch->close();

            if ($usuario) {
                $hash_almacenado = $usuario['contrasena'];

                // 3. Verificar la contraseña actual
                if (password_verify($contrasena_actual, $hash_almacenado)) {
                    // 4. Generar nuevo hash
                    $nuevo_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

                    // 5. Actualizar la contraseña
                    $sql_update = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
                    if ($stmt_update = $conexion->prepare($sql_update)) {
                        $stmt_update->bind_param("si", $nuevo_hash, $id_usuario_logueado);
                        
                        if ($stmt_update->execute()) {
                            $mensaje_texto = "✅ ¡Contraseña actualizada con éxito!";
                            $mensaje_tipo = "success";
                        } else {
                            $mensaje_texto = "❌ Error al actualizar la contraseña: " . $stmt_update->error;
                            $mensaje_tipo = "error";
                        }
                        $stmt_update->close();
                    } else {
                        $mensaje_texto = "❌ Error de preparación de la consulta de actualización: " . $conexion->error;
                        $mensaje_tipo = "error";
                    }
                } else {
                    $mensaje_texto = "❌ La contraseña actual es incorrecta.";
                    $mensaje_tipo = "error";
                }
            } else {
                $mensaje_texto = "❌ Error: No se pudo encontrar el usuario logueado en la base de datos.";
                $mensaje_tipo = "error";
            }
        } else {
            $mensaje_texto = "❌ Error de preparación para buscar la contraseña: " . $conexion->error;
            $mensaje_tipo = "error";
        }
    }
}


// ----------------------------------------------------
// --- 3. Lógica para OBTENER la Lista de Administradores ---
// ----------------------------------------------------
// Consulta para seleccionar solo los usuarios con rol 'admin'
$sql_select = "SELECT id, nombre, correo, fecha_registro FROM usuarios WHERE rol = 'admin' ORDER BY nombre ASC";
$resultado = $conexion->query($sql_select);

// Inicializa el array de administradores
$administradores = []; 
if ($resultado) {
    $administradores = $resultado->fetch_all(MYSQLI_ASSOC);
    $resultado->free(); // Libera la memoria del resultado
} else {
    // Si hay un error en la consulta SELECT, actualiza el mensaje de error
    $mensaje_texto = "❌ Error al obtener la lista de administradores: " . $conexion->error;
    $mensaje_tipo = "error";
}

// Cierra la conexión a la base de datos (se debe hacer aquí antes de cualquier HTML)
if (isset($conexion)) {
    $conexion->close();
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
            padding: 1.5rem 1rem;
            color: #eee;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0 0 0 / 35%);
            background-color: var(--secondarycolor);
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        section form {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            flex: 1;
            gap: 8px;
            justify-content: space-around
        }

        .form-group {
            display: flex;
            gap: 1rem;
            width: 100%;
            flex-wrap: wrap;
        }

        .form-group div {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .asignatura {
            color: white;
            border-radius: 0.5rem;
        }

        .contenedor-titulo {
            max-width: 300px;
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .contenedor-titulo h2 {
            margin: 0;
        }

        #toast {
            min-width: 250px;
            margin-left: -125px; /* Centrar */
            background-color: #333;
            backdrop-filter: blur(10px);
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 1000;
            right: 30px;
            top: 30px;
            transform: translateY(-50px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transition: 0.5s ease;
        }
        /* Clases de color para el toast */
        #toast.success { background-color: rgba(44, 171, 74, 0.8); border: 1px solid rgba(31, 127, 53, 1); }
        #toast.error { background-color: rgba(176, 43, 56, 0.8); border: 1px solid rgba(176, 43, 56, 1); }
        #toast.warning { background-color: rgba(196, 149, 7, 0.8); color: #333; border: 1px solid rgba(196, 149, 7, 1); }

        /* Mostrar el toast (usado por JS) */
        #toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        table {
            background-color: rgba(255, 255, 255, 0.85);
            color: var(--tertiarycolor);
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
            <p>Aquí podrás configurar diferentes apartados del sistema Luminary.</p>
        </div>
        <div id="toast"></div>
        <section>
            <div class="contenedor-titulo">
                <h2>Registrar Nuevo Administrador</h2>
                <p>Rellena el siguiente formulario para agregar un nuevo administrador al sistema.</p>
            </div>
            
            

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <div>
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" required>
                    </div>
                    <div>
                        <label for="contrasena">Contraseña:</label>
                        <input type="password" id="contrasena" name="contrasena" required>
                    </div>
                    
                </div>
                <div class="form-group" style="justify-content: flex-end;">
                  <button type="submit">Registrar Administrador</button>
                </div>
                
            </form>
        </section>
        <section style="flex-direction: column;">
            <h2>Gestión de Administradores</h2>

            <?php if (count($administradores) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Fecha de Registro</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($administradores as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['id']); ?></td>
                            <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($admin['correo']); ?></td>
                            <td><?php echo htmlspecialchars($admin['fecha_registro']); ?></td>
                            <td>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style=" align-items: center;">
                                    <input type="hidden" name="eliminar_id" value="<?php echo $admin['id']; ?>">
                                    <button type="submit" class="eliminar" style="width: fit-content;" onclick="return confirm('¿Está seguro de que desea eliminar al administrador <?php echo htmlspecialchars($admin['nombre']); ?>?');">
                                        <img src="/assets/icons/delete.svg" alt="Eliminar">
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron administradores.</p>
            <?php endif; ?>
        </section>
        <section>
            <div class="contenedor-titulo">
                <h2>Cambiar contraseña</h2>
                <p>Cambia tu contraseña para mantener tu cuenta segura.</p>
                
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="cambiar_contrasena" value="1">
                <div class="form-group">
                    <div>
                        <label for="contrasena_actual">Contraseña Actual:</label>
                        <input type="password" id="contrasena_actual" name="contrasena_actual" required>
                    </div>
                    <div>
                        <label for="nueva_contrasena">Nueva Contraseña:</label>
                        <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                    </div>
                </div>
                <a href="">¿Has olvidado la contraseña?</a>
                
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit">Cambiar Contraseña</button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Variables PHP pasadas a JavaScript
    const mensajeTexto = "<?php echo addslashes($mensaje_texto); ?>";
    const mensajeTipo = "<?php echo addslashes($mensaje_tipo); ?>";
    const toast = document.getElementById("toast");

    if (mensajeTexto && mensajeTipo) {
        // Asigna el contenido y el tipo de estilo
        toast.textContent = mensajeTexto;
        toast.classList.add(mensajeTipo);
        toast.classList.add("show");

        // Oculta el toast después de 5 segundos
        setTimeout(function(){ 
            toast.classList.remove("show");
            toast.classList.remove(mensajeTipo);
        }, 2500); 
    }
});

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