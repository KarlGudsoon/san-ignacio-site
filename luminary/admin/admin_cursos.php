<?php
include '../conexion.php';
session_start();

// Verifica si el usuario es admin
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// Obtener cursos
$result = $conexion->query("SELECT * FROM cursos ORDER BY nivel, letra");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Directivo - Cursos</title>
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

        .curso {
            width: fit-content;
            background-color: #035bad;
            box-shadow: inset ;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            height: fit-content;
            border: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5), inset 0 0 2px rgba(255, 255, 255, 0.5);
        }

        .curso a {
            text-decoration: none;
        }

        .curso:hover {
            filter: brightness(95%);
        }

        .curso:active {
            scale: 0.95
        }

        button {
            width: fit-content;
        }

        .contenedor-principal {
            flex-direction: column;
        }

        .contenedor-cursos {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 0.5rem;
            height: fit-content;
            flex-wrap: wrap;
        }

        .contenedor-cursos h2 {
            margin: 0;
            margin-right: 0.5rem;
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
            <h1>Administrar cursos</h1>
            <p>Aquí podrás gestionar a todos los cursos del establecimientos.</p>
            <h1>Cursos disponibles</h1>
        </div>

        <div class="contenedor-principal">
            <ul class="contenedor-cursos">
                <h2>1° Nivel</h2>
                <?php
                $result->data_seek(0);
                while ($curso = $result->fetch_assoc()):
                    if ($curso['nivel'] == '1°'):
                ?>
                    <li class="curso">
                        <a href="ver_curso.php?id=<?= $curso['id'] ?>">
                            <?= $curso['nivel'] . ' Nivel ' . $curso['letra'] ?>
                        </a>
                    </li>
                <?php endif; endwhile; ?>
            </ul>

            <ul class="contenedor-cursos">
                <h2>2° Nivel</h2>
                <?php
                $result->data_seek(0); // Volver al inicio para repetir el ciclo
                while ($curso = $result->fetch_assoc()):
                    if ($curso['nivel'] == '2°'):
                ?>
                    <li class="curso">
                        <a href="ver_curso.php?id=<?= $curso['id'] ?>">
                            <?= $curso['nivel'] . ' Nivel ' . $curso['letra'] ?>
                        </a>
                    </li>
                <?php endif; endwhile; ?>
            </ul>
        </div>

    </main>
    <?php
    include "components/aside_bottom.php"
    ?>

    
</body>
</html>

<script>

let asignaturas = document.querySelectorAll('.curso');

asignaturas.forEach((element) => {
    const texto = element.textContent.trim();

    if (texto.includes("1° Nivel")) {
        element.style.backgroundColor = "#0da761"; // Verde
    } else if (texto.includes("2° Nivel")) {
        element.style.backgroundColor = "#3891e9"; // Azul
    }
});

</script>


