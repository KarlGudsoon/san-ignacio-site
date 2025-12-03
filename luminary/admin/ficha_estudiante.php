<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

$profesor_id = $_SESSION['id'];

$estudiante_id = $_GET['id'] ?? null;
if (!$estudiante_id) {
    echo "ID de estudiante no proporcionado.";
    exit;
}

// Obtener información del estudiante
$estudiante_sql = $conexion->prepare("
    SELECT matricula_id 
    FROM estudiantes 
    WHERE id = ?
");
$estudiante_sql->bind_param("i", $estudiante_id);
$estudiante_sql->execute();
$estudiante_result = $estudiante_sql->get_result();

if ($estudiante_result->num_rows === 0) {
    echo "Estudiante no encontrado.";
    exit;
}

$estudiante = $estudiante_result->fetch_assoc();
$matricula_id = $estudiante['matricula_id'];

$matricula_sql = $conexion->prepare("
    SELECT nombre_estudiante, apellidos_estudiante, rut_estudiante
    FROM matriculas 
    WHERE id = ?
");
$matricula_sql->bind_param("i", $matricula_id);
$matricula_sql->execute();
$matricula_result = $matricula_sql->get_result();

if ($matricula_result->num_rows === 0) {
    echo "Matrícula no encontrada.";
    exit;
}

$matricula = $matricula_result->fetch_assoc();

$cursos_jefatura = $conexion->query("
    SELECT c.id, c.nivel, c.letra
    FROM cursos c
    WHERE c.profesor_jefe_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

$primerCursoJefatura = $cursos_jefatura->fetch_assoc();
$cursos_jefatura->data_seek(0);

$estudiante = $estudiante_result->fetch_assoc();

$curso_sql = $conexion->prepare("SELECT curso_id FROM estudiantes WHERE id = ?");
$curso_sql->bind_param("i", $estudiante_id);
$curso_sql->execute();
$curso_result = $curso_sql->get_result();

if ($curso_result->num_rows === 0) {
    echo "Curso no encontrado.";
    exit;
}
$curso_id = $curso_result->fetch_assoc()['curso_id'];

$stmt = $conexion->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    echo "Curso no encontrado.";
    exit;
}



// Obtener asignaturas y notas del estudiante
$notas_sql = $conexion->prepare("
    SELECT a.nombre AS asignatura,
           n.nota1, n.nota2, n.nota3, n.nota4, n.nota5, n.nota6, n.nota7, n.nota8, n.nota9, n.x̄
    FROM curso_asignatura ca
    INNER JOIN asignaturas a ON ca.asignatura_id = a.id
    LEFT JOIN notas n ON a.id = n.asignatura_id AND n.estudiante_id = ?
    WHERE ca.curso_id = ?
    ORDER BY a.nombre
");
$notas_sql->bind_param("ii", $estudiante_id, $curso_id);
$notas_sql->execute();
$notas_result = $notas_sql->get_result();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de <?= htmlspecialchars($matricula['nombre_estudiante'] . " " . $matricula['apellidos_estudiante']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/global.css">   
    <link rel="stylesheet" href="style.css"> 
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">  
    <style>
        a {
            text-decoration: none;
            color: inherit;
        }

        .asignatura {
            font-size: 1rem;
            color: white;
            border-radius: 0.5rem;
            background-color: #035bad;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .contenedor-botones {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
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
            color: white;
            margin-top: 1rem;
        }

        .curso a {
            text-decoration: none;
        }

        .curso:active {
            scale: 0.95
        }
    
    </style>
</head>
<body>
    <?php
    include "components/aside.php"
    ?>
    <main>
        <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
        <h1 style="margin-top: 1rem;">Ficha del Estudiante</h1>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($matricula['nombre_estudiante'] . " " . $matricula['apellidos_estudiante']) ?></p>
        <p><strong>RUT:</strong> <?= htmlspecialchars($matricula['rut_estudiante']) ?></p>
        <p><b>Curso: </b><span class="curso" style="font-weight: 300;"><?= htmlspecialchars($curso['nivel'] . " Nivel " . $curso['letra']) ?></span></p>

        <h2>Notas por Asignatura</h2>
        <table border="1">
            <tr>
                <th>Asignatura</th>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Nota 3</th>
                <th>Nota 4</th>
                <th>Nota 5</th>
                <th>Nota 6</th>
                <th>Nota 7</th>
                <th>Nota 8</th>
                <th>Nota 9</th>
                <th>x̄</th>
            </tr>
            <?php if ($notas_result->num_rows > 0): ?>
                <?php while ($nota = $notas_result->fetch_assoc()): ?>
                <tr>
                    <td class="asignatura"><?= htmlspecialchars($nota['asignatura']) ?></td>
                    <td><?= $nota['nota1'] ?? '-' ?></td>
                    <td><?= $nota['nota2'] ?? '-' ?></td>
                    <td><?= $nota['nota3'] ?? '-' ?></td>
                    <td><?= $nota['nota4'] ?? '-' ?></td>
                    <td><?= $nota['nota5'] ?? '-' ?></td>
                    <td><?= $nota['nota6'] ?? '-' ?></td>
                    <td><?= $nota['nota7'] ?? '-' ?></td>
                    <td><?= $nota['nota8'] ?? '-' ?></td>
                    <td><?= $nota['nota9'] ?? '-' ?></td>
                    <td class="promedio"><strong><?= $nota['x̄'] ?? '-' ?></strong></td>
                </tr>
                <tr>
                    <td colspan="13" style="height: 1px; border-bottom: 1px solid #035bad; padding: 0; opacity: 0.2;"></td>
                </tr>
                <?php endwhile; ?>
                
            <?php else: ?>
                <tr>
                    <td colspan="8">No hay notas registradas.</td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="contenedor-botones">
            <button><a href="../editor/descarga_notas_csv.php?id=<?= $estudiante_id ?>" target="_blank"><img src="/assets/icons/streamline-ultimate--microsoft-excel-logo.svg" alt=""> Descargar notas en CSV</a></button>
            <button><a href="../editor/descarga_notas_pdf.php?id=<?= $estudiante_id ?>" target="_blank"><img src="/assets/icons/streamline--convert-pdf-2-solid.svg" alt=""> Descargar informe de notas</a></button>
        </div>

        
        
        
        
        
    </main>
    
    <?php
        include "components/aside_bottom.php"
    ?>         
    
</body>
</html>

<script src="../editor/script.js"></script>

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
    } else if (element.innerHTML === "Inglés") {
        element.style.backgroundColor = "#cdb51a"; 
    } else if (element.innerHTML === "Inglés Comunicativo") {
        element.style.backgroundColor = "#23babf"; 
    } else if (element.innerHTML === "TICs") {
        element.style.backgroundColor = "#8544cf"; 
    }
});

let cursos = document.querySelectorAll('.curso');

cursos.forEach((element) => {
    const texto = element.textContent.trim();

    if (texto.includes("1° Nivel")) {
        element.style.backgroundColor = "#0da761"; // Verde
    } else if (texto.includes("2° Nivel")) {
        element.style.backgroundColor = "#3891e9"; // Azul
    }
});
</script>
