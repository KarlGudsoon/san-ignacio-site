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

        section {
            padding: 0 1rem;
        }

        .asignatura {
            color: white;
            border-radius: 0.5rem;
        }
        
    </style>
</head>
<body>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li class="seleccionada"><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
                <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>      
            </ul>
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>
    <main>
        <div class="contenedor-informacion"> 
            <h1>Bienvenido al Panel Directivo, <?= htmlspecialchars($_SESSION['nombre']) ?></h1>
            <p>Aquí podrás gestionar tus cursos asignados. Utiliza el menú de navegación para acceder a las diferentes secciones.</p>
        </div>
        
        <div class="contenedor-principal">
            <div class="contenedor-interno-1" style="flex:1;">
                <div class="section horario"> 
                    <h1>Horario</h1>
                </div> 
            </div>  
            <div class="contenedor-interno-2">
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
        </div>

        
    </main>
    <aside class="nav-bottom">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li class="seleccionada"><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
                <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li> 
            </ul>
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>

    
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