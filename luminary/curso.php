<?php
include 'conexion.php';

// Obtener ID del curso
$curso_id = $_GET['id'] ?? null;
if (!$curso_id || !is_numeric($curso_id)) {
    echo "Curso no válido.";
    exit;
}

// Obtener información del curso
$stmt = $conexion->prepare("SELECT c.nivel, c.letra, u.nombre, u.correo FROM cursos c INNER JOIN usuarios u ON c.profesor_jefe_id = u.id  WHERE c.id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    echo "Curso no encontrado.";
    exit;
}

function asignarJornadaPorCurso($curso) {
    // Definir qué cursos van en cada jornada
    $jornadas = [
        'Mañana' => [1, 4, 5],
        'Tarde' => [2, 6, 7],
        'Noche' => [3, 8, 9]
    ];
    
    // Buscar el curso en cada jornada
    foreach ($jornadas as $jornada => $cursos) {
        if (in_array($curso, $cursos)) {
            return $jornada;
        }
    }
    
    return 'Sin información'; // Jornada por defecto si no se encuentra
}

$jornada = asignarJornadaPorCurso($curso_id);

$horario = "";
$modalidad_icon = "";

if ($jornada === "Mañana") {
    $horario = "8:15 a 13:30";
    $modalidad_icon = "/assets/icon/meteocons--sunset-fill.svg";
} elseif ($jornada === "Tarde") {
    $horario = "14:00 a 18:00";
    $modalidad_icon = "/assets/icon/line-md--sunny-filled-loop.svg";
} elseif ($jornada === "Noche") {
    $horario = "18:00 a 23:00";
    $modalidad_icon = "/assets/icon/line-md--moon-filled-alt-loop.svg";
} else {
    $horario = "Sin horario definido";
    $modalidad_icon = "";
}

// Obtener asignaturas del curso y profesor de asignatura
$stmt = $conexion->prepare("SELECT 
    ca.curso_id,
    ca.asignatura_id,
    a.nombre AS asignatura,
    u.nombre AS profesor
    FROM curso_asignatura ca
    INNER JOIN asignaturas a ON ca.asignatura_id = a.id
    LEFT JOIN curso_profesor cp ON cp.curso_id = ca.curso_id 
                            AND cp.asignatura_id = ca.asignatura_id
    LEFT JOIN usuarios u ON u.id = cp.profesor_id
    WHERE ca.curso_id = ?;
");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$asignaturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $curso['nivel']. " Nivel " . $curso['letra'] ?></title>
    <link rel="stylesheet" href="/public/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
    <style>
        .asignatura {
            transition: 0.1s ease;
            cursor: pointer;
        }
        .asignatura:hover {
            scale: 1.05;
        }
        .asignatura::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 15%);
            z-index: 0;
        }

        .curso {
            width: fit-content;
            background-color: #035bad;
            color: #eee;
            box-shadow: inset ;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            height: fit-content;
            border: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5), inset 0 0 2px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    
</body>
    <section class="datos">
        <div class="contenedor-datos p-2">
            <span class="d-flex gap-2 align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="var(--secondarycolor)" d="M20.891 2.006L20.997 2l.13.008l.09.016l.123.035l.107.046l.1.057l.09.067l.082.075l.052.059l.082.116l.052.096q.07.15.09.316l.005.106q0 .113-.024.22l-.035.123l-6.532 18.077A1.55 1.55 0 0 1 14 22.32a1.55 1.55 0 0 1-1.329-.747l-.065-.127l-3.352-6.702l-6.67-3.336a1.55 1.55 0 0 1-.898-1.259L1.68 10c0-.56.301-1.072.841-1.37l.14-.07l18.017-6.506l.106-.03l.108-.018z" />
                </svg>
                Santa Ana 95 Paradero 7 Villa Alemana
            </span>
            <span class="d-flex gap-2 align-items-center">
                Dudas o consultas
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15">
                    <path fill="var(--secondarycolor)" d="M2.5 0A2.5 2.5 0 0 0 0 2.5v2C0 10.299 4.701 15 10.5 15h2a2.5 2.5 0 0 0 2.5-2.5v-1.382a1.5 1.5 0 0 0-.83-1.342l-2.415-1.208a1.5 1.5 0 0 0-2.094.868l-.298.893a.71.71 0 0 1-.812.471A5.55 5.55 0 0 1 4.2 6.45a.71.71 0 0 1 .471-.812l1.109-.37a1.5 1.5 0 0 0 .98-1.787l-.586-2.344A1.5 1.5 0 0 0 4.72 0z" />
                </svg>
                <a href="tel:322534253">32 2534253</a>
                </span>
        </div>
        
    </section>
    <section class="navegador">
        <nav>  
            <button class="abrir-navegacion d-block d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                <img src="/assets/icons/menu.svg" alt="">
            </button>
            <div class="contenedor-logo-nav">
                <img class="logo" src="/assets/icons/logo-2.svg" alt="Logo Centro de Estudios San Ignacio">
                <span>Centro de Estudios San Ignacio Villa Alemana</span>
            </div>
            
            <ul class="d-none d-md-flex">
                <li><a href="/">Inicio</a></li>
                <li><a href="/pages/estudiantes.html">Estudiantes</a></li>
                <li><a href="/pages/admision.html">Admisión</a></li>
                <div class="dropdown">
                    <button class="btn-nav dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sobre nosotros
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="/pages/reglamentos.html">Reglamentos</a></li>
                        <li><a href="/pages/mision-vision.html">Misión y Visión</a></li>
                        <li><a href="/pages/nosotros.html">Nosotros</a></li>
                    </ul>
                </div>
                <li><a href="/#contacto">Contacto</a></li>
                <li><a class="boton-navegacion" href="/luminary/">Ingresar</a></li>
            </ul>
            
        </nav>

    </section>



    <div class="offcanvas offcanvas-start bg-blue" tabindex="19999" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Navegación</h5>
        <button type="button" class="btn-close dark" data-bs-theme="dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="contenedor-logo-nav">
            <img class="logo" src="/assets/icons/logo-2.svg" alt="Logo Centro de Estudios San Ignacio">
            <span>Centro de Estudios San Ignacio Villa Alemana</span>
        </div>
        <ul class="lista-navegacion">
            <li><a href="/">Inicio</a></li>
            <li><a href="/pages/admision.html">Admisión</a></li>
            <li><a href="/pages/estudiantes.html">Estudiantes</a></li>
            <div class="dropdown">
                <li class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Sobre nosotros
                </li>
                <ul class="dropdown-menu">
                    <li><a href="/pages/reglamentos.html">Reglamentos</a></li>
                    <li><a href="/pages/mision-vision.html">Misión y Visión</a></li>
                    <li><a href="/pages/nosotros.html">Nosotros</a></li>
                </ul>
            </div>
            <li><a href="/#contacto">Contacto</a></li>
            <a class="boton-navegacion" href="/luminary/">Ingresar</a>
        </ul>
    </div>
    </div>
    <main class="color-tertiary ">
        <section class="color-tertiary mt-4 d-flex justify-content-center">
            <div class="p-4 pb-0 rounded-4 d-flex justify-content-md-between gap-4 flex-wrap justify-content-center" style="width: 1000px">
                <div>
                    <h1 class="curso"><?= $curso['nivel']. " Nivel " .$curso['letra']?></h1>
                    <span><?= $curso['nivel'] = "1°" ? "(1° y 2° medio)" : "(3° y 4° medio)" ?></span>
                    <ul class="d-flex mt-2 gap-4 list-unstyled">
                        <li class="d-flex flex-column align-items-center">
                            <img width="75px" src="<?= $modalidad_icon ?>" alt="">
                            <strong class="mb-2">Modalidad:</strong>
                            <span><?= $jornada ?></span>
                        </li>
                        <li class="d-flex flex-column align-items-center" style="max-width: 100px">
                            <img width="75px" class="p-3" src="/assets/icon/time-black.svg" alt="">
                            <strong class="mb-2">Horas:</strong>
                            <span class="text-center"><?= $horario ?></span>
                        </li>
                        <li class="d-flex flex-column align-items-center" style="max-width: 100px">
                            <img width="75px" class="p-3" src="/assets/icon/horario.svg" alt="">
                            <strong class="mb-2">Horario:</strong>
                            <a class="link px-2 py-1" style="font-size: 14px;"  href="">Descargar</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div class="rounded-4" style="width: 100px; height: 100px; background-color: rgba(0, 0, 0, 15%);">
                        <img src="" alt="">
                    </div>
                    <p class="fw-bold mt-3 mb-2">Profesor Jefe</p>
                    <span><?= $curso['nombre'] ?></span>
                    <a class="link-simple" href="mailto:<?= $curso['correo'] ?>"><?= $curso['correo'] ?></a>
                </div>
            </div>
        </section>
        
        <section class="container-md my-2 d-flex flex-column justify-content-md-between flex-wrap justify-content-center" style="max-width: 1000px">
            <span class="border-bottom border-1 border-dark w-100 mt-2 mb-2 opacity-25 d-block" style="height: 1px; width: 100%;"></span>
            <h2 class="mb-4 mt-4 opacity-25 text-black">Asignaturas</h2>
            <div class="row">
                <?php foreach ($asignaturas as $asignatura): ?>
                    <?php
                    $icon = "/assets/icon/default.svg";

                    if ($asignatura['asignatura'] === "Lenguaje") {
                        $icon = "/assets/icon/books.svg";
                    }
                    elseif ($asignatura['asignatura'] === "Matemáticas") {
                        $icon = "/assets/icon/math.svg";
                    }
                    elseif ($asignatura['asignatura'] === "Estudios Sociales") {
                        $icon = "/assets/icon/history.svg";
                    } elseif ($asignatura['asignatura'] === "Inglés") {
                        $icon = "/assets/icon/english.svg";
                    } elseif ($asignatura['asignatura'] === "TIC") {
                        $icon = "/assets/icon/computer.svg";
                    } elseif ($asignatura['asignatura'] === "Artes Visuales") {
                        $icon = "/assets/icon/photo.svg";
                    } elseif ($asignatura['asignatura'] === "Filosofía") {
                        $icon = "/assets/icon/thinking.svg";
                    } elseif ($asignatura['asignatura'] === "Ciencias") {
                        $icon = "/assets/icon/science.svg";
                    }
                    ?>
                    <div class="col-md-4 p-2">
                        <div class="position-relative p-3 rounded-4 asignatura color-primary overflow-hidden" data-asignatura="<?= $asignatura['asignatura_id'] ?>" style="border-left: 6px solid; box-shadow: 2px 2px 5px rgba(0, 0, 0, 25%);">
                            <h3 class="h5 z-3 position-relative"><?= $asignatura['asignatura'] ?></h3>
                            <p class="position-relative z-3"><?= $asignatura['profesor'] === NULL ? "Sin información" : "Prof. ".$asignatura['profesor'] ?></p>
                            <img class="z-3 position-absolute bottom-0 end-0 opacity-25" height="100%" src="<?= $icon ?>" alt="">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </section>

    </main>
<footer>
    <div class="contenedor-footer flex-wrap">
        <div class="seccion-footer d-flex flex-column gap-3 align-items-start" style="min-width: 200px;">
            <img src="/assets/icons/logo-2.svg" alt="">
            <p>El Centro de Estudios San Ignacio de Villa Alemana es una comunidad educativa para jóvenes y adultos, orientada al desarrollo integral, la formación humanista-cristiana y el acompañamiento académico hacia nuevas oportunidades educativas y laborales.</p>
            <div class="redes-sociales d-flex gap-3">
                <a href="https://wa.me/56996116669" target="_blank"><img class="h-100" src="/assets/icons/whatsapp.svg" alt=""></a>
                <a href="https://www.instagram.com/sanignaciova/" target="_blank"><img class="h-100" src="/assets/icons/instagram.svg" alt=""></a>
            </div>
        </div>
        <div class="d-flex flex-fill">
            <div class="seccion-footer d-flex flex-column gap-3 justify-content-center">
                <a class="link-simple" href="/">Inicio</a>
                <a class="link-simple" href="/pages/estudiantes.html">Estudiantes</a>
                <a class="link-simple" href="/pages/nosotros.html">Sobre nosotros</a>
                <a class="link-simple" href="/#contacto">Contacto</a>
            </div>
            <div class="seccion-footer d-flex flex-column gap-3 justify-content-center">
                <a class="link-simple" href="/pages/reglamentos.html">Reglamentos</a>
                <a class="link-simple" href="">PIE</a>
                <a class="link-simple" href="/pages/mision-vision.html">Misión y visión</a>
            </div>
        </div>
        
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 15%);">
        <span >&copy; 2025 Centro de Estudios San Ignacio. Todos los derechos reservados.</span>
        <span>Página Web desarrollada por <a target="_blank" href="https://www.linkedin.com/in/adri%C3%A1n-maturana-mu%C3%B1oz-3a7b501aa/">Adrián Maturana</a></span>
    </div>
    
</footer>
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
        element.style.borderColor = "#ff8f1eff"; 
        element.style.backgroundColor = "#ff8f1eff"; 
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>