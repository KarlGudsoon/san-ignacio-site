<?php
// curso.php
include '../../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}


$profesor_id = $_SESSION['id'];
$curso_id = $_GET['curso_id'];
$asignatura_id = $_GET['asignatura_id'];

// Verificar que el profesor tenga acceso a este curso y asignatura
$verificar = $conexion->query("
    SELECT cp.id, c.nivel, c.letra, a.nombre AS asignatura, a.id AS asignatura_id
    FROM curso_profesor cp
    JOIN cursos c ON cp.curso_id = c.id
    JOIN asignaturas a ON cp.asignatura_id = a.id
    WHERE cp.profesor_id = $profesor_id
    AND cp.curso_id = $curso_id
    AND cp.asignatura_id = $asignatura_id
");

if ($verificar->num_rows === 0) {
    echo "No tienes acceso a este curso.";
    exit;
}

$datos_curso = $verificar->fetch_assoc();

// Obtener estudiantes de este curso
$estudiantes = $conexion->query("
    SELECT e.id, m.nombre_estudiante, m.apellidos_estudiante, m.rut_estudiante
    FROM estudiantes e
    JOIN matriculas m ON e.matricula_id = m.id
    WHERE e.curso_id = $curso_id
    ORDER BY m.apellidos_estudiante, m.nombre_estudiante
");

// Obtener notas de los estudiantes para esta asignatura
$notas = [];
if ($estudiantes->num_rows > 0) {
    while ($est = $estudiantes->fetch_assoc()) {
        $estudiante_id = $est['id'];
        $query_notas = $conexion->query("
            SELECT nota1, nota2, nota3, nota4, nota5, nota6, nota7, nota8, nota9, x
            FROM notas
            WHERE estudiante_id = $estudiante_id
            AND asignatura_id = $asignatura_id
            AND profesor_id = $profesor_id
        ");
        
        $notas[$estudiante_id] = $query_notas->fetch_assoc() ?: [];
    }
    $estudiantes->data_seek(0); // Resetear el puntero del resultado
}

$icon = "/assets/icon/default.svg";

if ($datos_curso['asignatura'] === "Lenguaje") {
    $icon = "/assets/icon/books.svg";
}
elseif ($datos_curso['asignatura'] === "Matemáticas") {
    $icon = "/assets/icon/math.svg";
}
elseif ($datos_curso['asignatura'] === "Estudios Sociales") {
    $icon = "/assets/icon/history.svg";
} elseif ($datos_curso['asignatura'] === "Inglés") {
    $icon = "/assets/icon/english.svg";
} elseif ($datos_curso['asignatura'] === "TIC") {
    $icon = "/assets/icon/computer.svg";
} elseif ($datos_curso['asignatura'] === "Artes Visuales") {
    $icon = "/assets/icon/photo.svg";
} elseif ($datos_curso['asignatura'] === "Filosofía") {
    $icon = "/assets/icon/thinking.svg";
} elseif ($datos_curso['asignatura'] === "Ciencias") {
    $icon = "/assets/icon/science.svg";
}

// NOTAS



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
    <title>Curso <?= $datos_curso['nivel'].$datos_curso['letra'] ?> - <?= $datos_curso['asignatura'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/global.css">   
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    ::view-transition-old(*), ::view-transition-new(*) {
       
        height: 100%;
        width: 100%;
    }

    table {
        border-collapse: separate;
        background-color: rgb(191 208 225);
    }

    body.no-scroll {
        overflow: hidden;
    }


    .boton-modal.active {
        background-color: #035bad !important;
        color: white;
        opacity: 100% !important;
        border: none !important;
        transition: background-color 0.2s ease;
    }

    .boton-modal span {
        display: block;
        background-color: #035bad;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 130%;
        border-radius: 1rem 1rem 0 0;
        z-index: -1;
        pointer-events: none;
        opacity: 0;
        transition: 0.2s ease;
    }

    .boton-modal span::after {
        content: "";
        width: 50%;
        height: 95%;
        position: absolute  ;
        left: -50%;
        top: 0;
        background-color:transparent;
        border-bottom-right-radius: 10px;
        box-shadow: 0px 10px #035bad;
        pointer-events: none;
        opacity: 0;
        transition: 0.2s ease;
    }

    .boton-modal span::before {
        content: "";
        width: 50%;
        height: 95%;
        position: absolute ;
        right: -50%;
        top: 0;
        background-color: transparent;
        border-bottom-left-radius: 10px;
        box-shadow: 0px 10px #035bad;
        pointer-events: none;
        opacity: 0;
        transition: 0.2s ease;
    }

    .boton-modal.active span, .boton-modal.active span::after, .boton-modal.active span::before {
        opacity: 1;
    }

    .contenedor {
        display: none;
        background-color: #035bad;
        padding: 1rem;
        border-radius: 1rem;
    }

    #form {
        width: 100%;
    }

    #form table {
        width: 100%;
    }

    .contenedor.active {
        display: block;
    }

    main {
        display: block;
    }
    

    .accordion-button {
        background-color: var(--tertiarycolor);
        transition: 0.2s ease;
        color: white;
        border-radius: 1rem !important;
        font-size: 21px;
        z-index: 9;
        box-shadow: 0px 0px 10px -5px rgba(0,0,0,0.25);
    }

    .accordion-button::after {
        filter: invert(1);
    }

    .accordion-button:not(.collapsed) {
        background-color: var(--tertiarycolor);

        color: white;
        box-shadow: none;
    }

    .accordion-button:hover {
        background-color: "";
        transition: 0.2s ease;
    }

    .accordion-button:active {
        scale: 1;
    }

    .accordion-item {
        margin-bottom: 0.5rem;
        border: 0;
        background-color: transparent;
    }
   

    .accordion-body {
        background-color: #035bad95;
        color: white;
        margin: 0rem 1rem;
        z-index: 0;
        box-shadow: -1px 10px 10px -5px rgba(0,0,0,0.25) inset;
        -webkit-box-shadow: -1px 10px 10px -5px rgba(0,0,0,0.25) inset;
        -moz-box-shadow: -1px 10px 10px -5px rgba(0,0,0,0.25) inset;
        border-radius: 0 0 1rem 1rem;
    }


    </style>
</head>
<body>
    <?php include "../components/aside.php" ?>
    
    <main>
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class=" position-absolute top-0 end-0 z-3 m-4" style="background-color: #1b9d2cff; backdrop-filter:blur(15px); color: white; padding: 10px; border: 1px solid #15572489; border-radius: 10px;">
        <?= $_SESSION['mensaje_exito'] ?>
            </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>
        <a class="volver mb-2 d-block" style="height: 30px;" href="/luminary/editor/cursos"><img style="height: 30px;" src="/assets/icons/arrow.svg"></a>
        <div class="contenedor-informacion asignatura rounded-4 position-relative p-4 shadow" style="view-transition-name: curso-<?= $curso_id ?>-asig-<?= $datos_curso['asignatura_id'] ?>;"> 
            <span class="curso d-block mb-2 py-1"><?= $datos_curso['nivel'].' Nivel '.$datos_curso['letra'] ?></span>
            <h3 class="color-primary"><?= $datos_curso['asignatura'] ?></h3>
            <img class="z-3 position-absolute bottom-0 end-0 opacity-25" height="100%" src="<?= $icon ?>" alt="">
        </div>
        <section class="d-flex gap-2 mb-2">
            <button class="boton-modal position-relative border-1 bg-transparent color-black opacity-50 shadow-none rounded-4 py-1 px-3 z-2"  style="border: 1px solid rgba(0 0 0 / 100%);">Inicio <span></span></button>
            <button class="boton-modal position-relative border-1 bg-transparent color-black opacity-50 shadow-none rounded-4 py-1 px-3 z-2" data-modal="contenedor-calificaciones" style="border: 1px solid rgba(0 0 0 / 100%);">Calificaciones <span></span></button>
            <button class="boton-modal position-relative border-1 bg-transparent color-black opacity-50 shadow-none rounded-4 py-1 px-3 z-2" data-modal="contenedor-contenido" style="border: 1px solid rgba(0 0 0 / 100%);">Contenido <span></span></button>
        </section>
        
        <section class="w-100 contenedor" id="contenedor-calificaciones">
            <form id="form" method="POST" action="guardar_notas_curso.php">
                <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
                <input type="hidden" name="asignatura_id" value="<?= $asignatura_id ?>">

                <table border="1" cellpadding="5">
                    <tr>
                        <th>Estudiante</th>
                        <?php for ($i = 1; $i <= 9; $i++): ?>
                            <th>Nota <?= $i ?></th>
                        <?php endfor; ?>
                        <th>Σ</th>
                        <th>x</th>
                        <th>x̄</th>
                    </tr>

                    <?php while ($e = $estudiantes->fetch_assoc()):
                        $notas = $conexion->query("
                            SELECT * FROM notas
                            WHERE estudiante_id = {$e['id']} AND asignatura_id = $asignatura_id AND profesor_id = $profesor_id
                        ")->fetch_assoc();
                    ?>
                    <tr>
                        <td class="estudiantes"><?= $e['nombre_estudiante']. ' ' .$e['apellidos_estudiante']  ?></td>
                        <?php for ($i = 1; $i <= 9; $i++): 
                            $campo = "nota$i";
                        ?>
                            <td>
                                <input type="number" step="0.1" min="1" max="7"
                                    name="notas[<?= $e['id'] ?>][<?= $campo ?>]"
                                    value="<?= $notas[$campo] ?? '' ?>">
                            </td>
                        <?php endfor; ?>
                        <td><?= $notas['Σ'] ?? '-' ?></td>
                        <td><?= $notas['x'] ?? '-' ?></td>
                        <td class="promedio"><?= $notas['x̄'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td colspan="13" style="height: 1px; border-bottom: 1px solid #035bad; padding: 0; opacity: 0.2;"></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <br>
                <button type="submit">Guardar cambios</button>
            </form>
        </section>
        <section class="contenedor" id="contenedor-contenido">
            <div class="accordion" id="accordionPanelsStayOpenExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">
                        Unidad 1
                    </button>
                    </h2>
                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <strong>This is the first item’s accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                        Unidad 2
                    </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <strong>This is the second item’s accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                        Unidad 3
                    </button>
                    </h2>
                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <strong>This is the third item’s accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                        Unidad 4
                    </button>
                    </h2>
                    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <strong>This is the fourth item’s accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It’s also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                    </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    
    <script>
    function editarNotas(estudiante_id) {
        // Redirigir a una página para editar notas
        window.location.href = `editar_notas.php?estudiante_id=${estudiante_id}&curso_id=<?= $curso_id ?>&asignatura_id=<?= $asignatura_id ?>&profesor_id=<?= $profesor_id ?>`;
    }
    </script>
    
    <?php include "../components/aside_bottom.php" ?>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
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

const notas = document.querySelectorAll("input[type='number']");


notas.forEach(nota => {
    nota.addEventListener("input", function () {
        const valor = parseFloat(this.value);
        if (!isNaN(valor) && valor < 4) {
            this.style.backgroundColor = "#ffcccc"; // Rojo claro
        } else if (!isNaN(valor) && valor >= 4) {
            this.style.backgroundColor = "#b3e9a9"; // Verde claro
        } else {
            this.style.backgroundColor = ""; // Sin color si no es un número válido
        }
    });
});

notas.forEach(nota => {
    const valor = parseFloat(nota.value);

    if (!isNaN(valor) && valor < 4) {
        nota.style.backgroundColor = "#ffcccc"; // Rojo claro
    } else if (!isNaN(valor) && valor >= 4) {
        nota.style.backgroundColor = "#b3e9a9"; // Verde claro
    }
});

const promedio = document.querySelectorAll(".promedio"); 

promedio.forEach(elemento => {
    const valor = parseFloat(elemento.textContent);

    if (valor < 4) {
        elemento.style.backgroundColor = "#e24a4a";
    } else if (valor >= 4) {
        elemento.style.backgroundColor = "#2589df"; 
    }
});

let cerrarContenedor = document.querySelectorAll(".cerrar-contenedor");

cerrarContenedor.forEach(function(element) {
  element.addEventListener("click", function() {
    this.parentElement.classList.remove("active");
    document.body.classList.remove("no-scroll");
  });
});

function AbrirContenedor() {
    const idContenedor = this.getAttribute("data-modal");
    const modal = document.getElementById(idContenedor);
    document.querySelectorAll(".contenedor").forEach(contenedor => {
        contenedor.classList.remove("active")
    });
  
    if (modal) {
      modal.classList.add("active");
      document.body.classList.add("no-scroll");
    }
  }

document.querySelectorAll("[data-modal]").forEach(boton => {
    boton.addEventListener("click", AbrirContenedor);
});

document.querySelectorAll("[data-modal]").forEach(boton => {
    boton.addEventListener("click", function () {

        // Quitar la clase "active" de todos
        document.querySelectorAll("[data-modal]").forEach(btn => {
            btn.classList.remove("active");
        });

        // Agregar la clase solo al clickeado
        this.classList.add("active");
    });
});



</script>   