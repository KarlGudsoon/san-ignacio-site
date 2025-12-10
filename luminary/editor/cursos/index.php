<?php
include '../../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

$profesor_id = $_SESSION['id'];

// 1. Obtener cursos donde el profesor está asignado como docente
$cursos_asignados = $conexion->query("
    SELECT cp.*, c.id AS id_curso, c.nivel AS nivel, c.letra AS letra, 
    a.nombre AS asignatura
    FROM curso_profesor cp
    JOIN cursos c ON cp.curso_id = c.id
    JOIN asignaturas a ON cp.asignatura_id = a.id
    WHERE cp.profesor_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

// 2. Obtener cursos donde el profesor es jefe de curso
$cursos_jefatura = $conexion->query("
    SELECT c.id, c.nivel, c.letra
    FROM cursos c
    WHERE c.profesor_jefe_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

$primerCursoJefatura = $cursos_jefatura->fetch_assoc();
$cursos_jefatura->data_seek(0);



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cursos - Luminary</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/global.css">   
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />   
    <style>
    ::view-transition-old(*), ::view-transition-new(*) {
        height: 100%;
        width: 100%;
    }
    </style>
</head>
<body>
    <?php 
    include "../components/aside.php"
     ?>
    <main>
        <div class="contenedor-informacion"> 
            <h1>Todos los cursos</h1>
            <p>Aquí podrás gestionar tus cursos asignados</p>
        </div>
        <div class="row">
                <?php foreach ($cursos_asignados as $curso): ?>
                    <?php
                    $icon = "/assets/icon/default.svg";

                    if ($curso['asignatura'] === "Lenguaje") {
                        $icon = "/assets/icon/books.svg";
                    }
                    elseif ($curso['asignatura'] === "Matemáticas") {
                        $icon = "/assets/icon/math.svg";
                    }
                    elseif ($curso['asignatura'] === "Estudios Sociales") {
                        $icon = "/assets/icon/history.svg";
                    } elseif ($curso['asignatura'] === "Inglés") {
                        $icon = "/assets/icon/english.svg";
                    } elseif ($curso['asignatura'] === "TIC") {
                        $icon = "/assets/icon/computer.svg";
                    } elseif ($curso['asignatura'] === "Artes Visuales") {
                        $icon = "/assets/icon/photo.svg";
                    } elseif ($curso['asignatura'] === "Filosofía") {
                        $icon = "/assets/icon/thinking.svg";
                    } elseif ($curso['asignatura'] === "Ciencias") {
                        $icon = "/assets/icon/science.svg";
                    }
                    ?>
                    <div class="col-md-4 p-2">
                        <a href="curso.php?curso_id=<?= $curso['curso_id'] ?>&asignatura_id=<?= $curso['asignatura_id'] ?>">
                            <div class="shadow-sm card-hover position-relative p-3 rounded-4 asignatura color-primary overflow-hidden" style="view-transition-name: curso-<?= $curso['curso_id'] ?>-asig-<?= $curso['asignatura_id'] ?>; " data-asignatura="<?= $curso['asignatura_id'] ?>">
                                <span class="curso d-block mb-2 py-1"><?= $curso['nivel'].' Nivel '.$curso['letra'] ?></span>
                                <h3 class="h5 z-3 position-relative"><?= $curso['asignatura'] ?></h3>
                                <img class="z-3 position-absolute bottom-0 end-0 opacity-25" height="100%" src="<?= $icon ?>" alt="">
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        


        
    </main>
    <?php 
    include "../components/aside_bottom.php"
     ?>
 </body>
</html>
<script>
let asignatura = document.querySelectorAll('.asignatura');

asignatura.forEach((element) => {
    let text = element.textContent.trim().toLowerCase();

    if (text.includes("ciencias")) {
        element.style.borderColor = "#0da761";
        element.style.backgroundColor = "#0da761";
    } 
    else if (text.includes("matem")) {
        element.style.borderColor = "#3891e9"; 
        element.style.backgroundColor = "#3891e9"; 
    } 
    else if (text.includes("lenguaj")) {
        element.style.borderColor = "#f75353"; 
        element.style.backgroundColor = "#f75353";
    } 
    else if (text.includes("social")) {
        element.style.borderColor = "#e97a0aff"; 
        element.style.backgroundColor = "#e97a0aff"; 
    } 
    else if (text.includes("artes")) {  
        element.style.borderColor = "#23babf"; 
        element.style.backgroundColor = "#23babf"; 
    } 
    else if (text.includes("inglés") || text.includes("ingles")) {
        element.style.borderColor = "#ebbc14ff"; 
        element.style.backgroundColor = "#ebbc14ff"; 
    } 
    else if (text.includes("tic")) {
        element.style.borderColor = "#8544cf"; 
        element.style.backgroundColor = "#8544cf"; 
    }
    else if (text.includes("filosof")) {
        element.style.borderColor = "#cf58dcff"; 
        element.style.backgroundColor = "#cf58dcff"; 
    }
});

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