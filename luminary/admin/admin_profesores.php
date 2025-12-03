<?php
session_start();

require_once '../conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

$consulta_asignaturas = $conexion->query("SELECT id, nombre FROM asignaturas");
$asignaturas = [];
while ($fila = $consulta_asignaturas->fetch_assoc()) {
    $asignaturas[] = $fila;
}

if (isset($_POST['registrar_editor'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena_plana = $_POST['contrasena'];
    $asignatura = $_POST['asignatura'];
    $fecha_registro = date('Y-m-d H:i:s');
    $rol = 'editor';

    // Encriptar contraseña
    $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

    // Verificar si el correo ya existe
    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $verificar_result = $verificar->get_result();

    if ($verificar_result->num_rows > 0) {
        echo "<p style='color:red;'>Ya existe un usuario con ese correo.</p>";
    } else {
        $insertar = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, fecha_registro, asignatura, rol) VALUES (?, ?, ?, ?, ?, ?)");
        $insertar->bind_param("ssssss", $nombre, $correo, $contrasena_hash, $fecha_registro, $asignatura, $rol);
        var_dump($asignatura);


        if ($insertar->execute()) {
            echo "<p style='color:green;'>Profesor registrado correctamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al registrar el profesor.</p>";
        }
    }
}

// Procesar eliminación de profesor
if (isset($_GET['eliminar_profesor'])) {
    $id_profesor = $_GET['eliminar_profesor'];
    
    // Verificar que el usuario a eliminar es un profesor (rol editor)
    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE id = ? AND rol = 'editor'");
    $verificar->bind_param("i", $id_profesor);
    $verificar->execute();
    $verificar_result = $verificar->get_result();
    
    if ($verificar_result->num_rows > 0) {
        // Iniciar transacción para asegurar la integridad de los datos
        $conexion->begin_transaction();
        
        try {
            $actualizar_jefatura = $conexion->prepare("UPDATE cursos SET profesor_jefe_id = NULL WHERE profesor_jefe_id = ?");
            $actualizar_jefatura->bind_param("i", $id_profesor);
            $actualizar_jefatura->execute();

            $eliminar_notas = $conexion->prepare("DELETE FROM notas WHERE profesor_id = ?");
            $eliminar_notas->bind_param("i", $id_profesor);
            $eliminar_notas->execute();

            // 2. Eliminar relaciones en curso_profesor
            $eliminar_relaciones = $conexion->prepare("DELETE FROM curso_profesor WHERE profesor_id = ?");
            $eliminar_relaciones->bind_param("i", $id_profesor);
            $eliminar_relaciones->execute();

            // 3. Eliminar al profesor
            $eliminar_profesor = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
            $eliminar_profesor->bind_param("i", $id_profesor);
            $eliminar_profesor->execute();
            // 1. Primero eliminar las relaciones en curso_profesor
            $eliminar_relaciones = $conexion->prepare("DELETE FROM curso_profesor WHERE profesor_id = ?");
            $eliminar_relaciones->bind_param("i", $id_profesor);
            $eliminar_relaciones->execute();
            
            // 2. Luego eliminar al profesor
            $eliminar_profesor = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
            $eliminar_profesor->bind_param("i", $id_profesor);
            $eliminar_profesor->execute();
            
            // Confirmar la transacción si todo salió bien
            $conexion->commit();
            
            echo "<p style='color:green;'>Profesor eliminado correctamente.</p>";
            // Redirigir para evitar reenvío del formulario
            header("Location: admin.php");
            exit;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conexion->rollback();
            echo "<p style='color:red;'>Error al eliminar el profesor: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>No se encontró el profesor o no tienes permisos.</p>";
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
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">   

    <style>
        input {
            width: auto;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: Outfit, sans-serif;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .section {
            display: flex;
            height: fit-content;
        }

        .section section {
            padding: 0;
        }

        .asignatura {
            color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .agregar-profesor {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(15px);
            background-color: rgba(0, 0, 0, 0.2);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .agregar-profesor.active {
            display: flex;
        }

        button {
            width: fit-content;
        }
        
    </style>
</head>
<body>
    <?php
    include "components/aside.php"
    ?>
    <main>
        <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
        <div class="contenedor-informacion"> 
            <h1>Gestión de cuerpo docente</h1>
            <p>Aquí podrás gestionar a cuerpo docente y registrar a nuevos profesores con su respectiva asignatura</p>
        </div>

        <button data-modal="agregar-profesor"><img src="/assets/icons/add.svg">Agregar profesor</button>
        
        <div class="contenedor-principal">
            <section>
                <h2>Listado de profesores registrados</h2>

                <table border="1" cellpadding="8">
                    <thead>
                        <tr>
                            <th>Nombre del profesor</th>
                            <th>Asignatura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Obtener profesores con su asignatura
                        $consulta_profesores = $conexion->query("
                            SELECT id, nombre, asignatura 
                            FROM usuarios 
                            WHERE rol = 'editor'
                        ");

                        if ($consulta_profesores->num_rows > 0) {
                            while ($fila = $consulta_profesores->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                                echo "<td class='asignatura'>" . htmlspecialchars($fila['asignatura']) . "</td>";
                                echo "<td>
                                        <button class='eliminar'>
                                        <a href='?eliminar_profesor=" . $fila['id'] . "' 
                                        onclick=\"return confirm('¿Estás seguro de que deseas eliminar este profesor?');\"
                                        ><img src='/assets/icons/delete.svg'></a>
                                        </button>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No hay profesores registrados aún.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>    
        </div>
        <section class="agregar-profesor modal" id="agregar-profesor">
            <div class="cerrar-contenedor"><img src="/assets/icons/close.svg"></div>
            <div class="section modal-contenido" style="flex: none;">
                    <section>
                        <h2>Registrar nuevo profesor</h2>
                        <form method="POST" action="">
                            <label for="nombre">Nombre completo:</label><br>
                            <input type="text" name="nombre" required><br>

                            <label for="correo">Correo electrónico:</label><br>
                            <input type="email" name="correo" required><br>

                            <label for="contrasena">Contraseña:</label><br>
                            <input type="password" name="contrasena" required><br>

                            <label for="asignatura">Asignatura:</label><br>
                            <select name="asignatura" required>
                                <option value="">-- Selecciona una asignatura --</option>
                                <?php foreach ($asignaturas as $asignatura): ?>
                                    <option value="<?= $asignatura['nombre'] ?>"><?= $asignatura['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select><br><br>

                            <button type="submit" name="registrar_editor">Agregar profesor</button>
                        </form>
                    </section>
                </div>
        </section>
    </main>
    <?php
    include "components/aside_bottom.php"
    ?>

    
</body>
</html>

<script src="../public/script.js"></script>

<script>

let asignatura = document.querySelectorAll('.asignatura');

asignatura.forEach((element) => {
    let text = element.textContent.trim().toLowerCase();

    if (text.includes("ciencias")) {
        element.style.backgroundColor = "#0da761";
    } 
    else if (text.includes("matem")) {
        element.style.backgroundColor = "#3891e9"; 
    } 
    else if (text.includes("lenguaj")) {
        element.style.backgroundColor = "#f75353"; 
    } 
    else if (text.includes("social")) {
        element.style.backgroundColor = "#ed861f"; 
    } 
    else if (text.includes("comunicativo")) {  
        element.style.backgroundColor = "#23babf"; 
    } 
    else if (text.includes("inglés") || text.includes("ingles")) {
        element.style.backgroundColor = "#cdb51a"; 
    } 
    else if (text.includes("tic")) {
        element.style.backgroundColor = "#8544cf"; 
    }
    else if (text.includes("filosof")) {
        element.style.backgroundColor = "#cf58dcff"; 
    }
});


</script>