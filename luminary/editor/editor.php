<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

$profesor_id = $_SESSION['id'];

// 1. Obtener cursos donde el profesor está asignado como docente
$cursos_asignados = $conexion->query("
    SELECT cp.*, c.nivel, c.letra, a.nombre AS asignatura
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
    <title>Panel del Profesor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/global.css">   
    <link rel="stylesheet" href="style.css">   
</head>
<body>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li class="seleccionada"><a href="editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
                <?php if ($primerCursoJefatura): ?>
                    <li>
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
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>
    <main>
        <div class="contenedor-informacion"> 
            <h1>Bienvenido al Panel del Profesor, <?= htmlspecialchars($_SESSION['nombre']) ?></h1>
            <p>Aquí podrás gestionar tus cursos asignados. Utiliza el menú de navegación para acceder a las diferentes secciones.</p>
        </div>
        
        <div class="contenedor-principal">
            <div class="contenedor-interno-1">
                <div class="section horario">
                    <h1>Horario</h1>
                </div>

                <div class="cursos-asignados">
                    <div class="section">
                    <h2>Cursos asignados como docente</h2>
                    <?php if ($cursos_asignados->num_rows > 0): ?>
                        <ul class="cursos-list">
                            <?php while ($curso = $cursos_asignados->fetch_assoc()): ?>
                                <li>
                                    <a href="ver_notas.php?curso_id=<?= $curso['curso_id'] ?>&asignatura_id=<?= $curso['asignatura_id'] ?>">
                                        <?= $curso['nivel'] . $curso['letra'] ?> - <?= $curso['asignatura'] ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>No tienes cursos asignados como docente.</p>
                    <?php endif; ?>
                    </div>

                    <div class="section jefatura">
                        <h2>Jefatura</h2>
                        <?php if ($cursos_jefatura->num_rows > 0): ?>
                            <ul class="cursos-list">
                                <?php while ($curso = $cursos_jefatura->fetch_assoc()): ?>
                                    <li>
                                        <a href="ver_curso_jefe.php?curso_id=<?= $primerCursoJefatura['id'] ?>">
                                            <?= $curso['nivel'] . $curso['letra'] ?>
                                        </a>
                                        <div class="jefatura-badge">Jefe de Curso</div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No eres jefe de ningún curso actualmente.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="contenedor-interno-2">
                <div class="section">
                    <h1>Información del Profesor</h1>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($_SESSION['nombre']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['correo']) ?></p>
                    <p><strong>Rol:</strong> <?= htmlspecialchars($_SESSION['rol']) ?></p>
                </div>
            </div>

            

            
        </div>

        
    </main>
    <aside class="nav-bottom">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li class="seleccionada"><a href="editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li><a href="notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
                <?php if ($primerCursoJefatura): ?>
                    <li>
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
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>

    
</body>
</html>