<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: /index.html");
    exit;
}

$curso_id = $_GET['curso_id'] ?? null;
$profesor_id = $_SESSION['id'];

// Verificar que el profesor es realmente jefe de este curso
$verificacion = $conexion->query("SELECT id FROM cursos WHERE id = $curso_id AND profesor_jefe_id = $profesor_id");
if ($verificacion->num_rows === 0) {
    header("Location: panel_profesor.php");
    exit;
}

$cursos_jefatura = $conexion->query("
    SELECT c.id, c.nivel, c.letra
    FROM cursos c
    WHERE c.profesor_jefe_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

$primerCursoJefatura = $cursos_jefatura->fetch_assoc();
$cursos_jefatura->data_seek(0);

// Obtener información del curso
$curso = $conexion->query("SELECT nivel, letra FROM cursos WHERE id = $curso_id")->fetch_assoc();

// Obtener estudiantes del curso
$estudiantes = $conexion->query("SELECT id, nombre, rut FROM estudiantes WHERE curso_id = $curso_id ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Jefatura de <?= $curso['nivel'] . $curso['letra'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/global.css">   
    <link rel="stylesheet" href="style.css">   
</head>

<style>
    table {
            width: auto;
            margin-bottom: 20px;
            border-spacing: 5px;
            border-collapse: separate;
            background-color:rgba(3, 91, 173, 0.2);
            border-radius: 1rem;
            padding: 1rem;
        }

        th {
            background-color: #035bad;
            color: white;
            padding: 8px;
            text-align: center;
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        
        td {
            padding: 8px;
            text-align: center;
            border: 0;
        }

        a {
            color: white;
            text-decoration: none;
        }
</style>

<body>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="/editor/editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="/editor/notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
                <?php if ($primerCursoJefatura): ?>
                    <li class="seleccionada">
                        <a href="ver_curso_jefe.php?curso_id=<?= $primerCursoJefatura['id'] ?>">
                            <img class="icon" src="/assets/icons/list.svg" title="Ver curso jefe">
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <img class="icon" style="filter: brightness(80%);" src="/assets/icons/list.svg" title="No tienes jefatura">
                    </li>
                <?php endif; ?>
                
            </ul>
            <a href="/logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>
    <main>
        <a class="volver" href="/editor/editor.php"><img src="/assets/icons/arrow.svg"></a>
        <div class="contenedor-informacion"> 
            <h1>Jefatura de Curso: <?= $curso['nivel'] . $curso['letra'] ?></h1>
        </div>
        
        <div class="contenedor-principal">
            <div class="contenedor-interno-1">
                <h2>Listado de Estudiantes</h2>
                    <table border="1">
                        <tr>
                            <th>Nombre</th>
                            <th>RUT</th>
                            <th>Acciones</th>
                        </tr>
                        <?php while ($estudiante = $estudiantes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $estudiante['nombre'] ?></td>
                            <td><?= $estudiante['rut'] ?></td>
                            <td>
                                <button><a href="ficha_estudiante.php?id=<?= $estudiante['id'] ?>">Ver ficha</a></button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="13" style="height: 1px; border-bottom: 1px solid #035bad; padding: 0; opacity: 0.2;"></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                <button style="margin-bottom: 1rem;"><a href="descarga_notas_csv_curso.php?curso_id=<?= $curso_id ?>"><img src="/assets/icons/streamline-ultimate--microsoft-excel-logo.svg" alt=""> Descargar todas las notas del curso</a></button>
                <button><a target="_blank" href="descarga_notas_pdf_curso.php?curso_id=<?= $curso_id ?>"><img src="/assets/icons/streamline--convert-pdf-2-solid.svg" alt="">Descargar informe de notas</a></button>
                
                

            </div>
        </div>
             

        
    </main>
    <aside class="nav-bottom">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="/editor/editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="/editor/notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
                <?php if ($primerCursoJefatura): ?>
                    <li class="seleccionada">
                        <a href="ver_curso_jefe.php?curso_id=<?= $primerCursoJefatura['id'] ?>">
                            <img class="icon" src="/assets/icons/list.svg" title="Ver curso jefe">
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <img class="icon" style="filter: brightness(80%);" src="/assets/icons/list.svg" title="No tienes jefatura">
                    </li>
                <?php endif; ?>
            </ul>
            <a href="/logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>

    
</body>
</html>