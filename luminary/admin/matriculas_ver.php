<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

if (!isset($_GET['id'])) {
    exit("ID no válido");
}

$id = intval($_GET['id']);

$query = "
    SELECT m.*, c.nivel, c.letra 
    FROM matriculas m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    WHERE m.id = $id
    LIMIT 1
";

$result = $conexion->query($query);

if ($result->num_rows === 0) {
    exit("Matrícula no encontrada.");
}

$d = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de Matrícula</title>
    <link rel="stylesheet" href="../public/global.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .card {
            padding: 2rem;
            background: rgba(255,255,255,0.15);
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            color: black;
        }
        .card h2 {
            margin-bottom: 1rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .campo {
            padding: .8rem;
            background: rgba(255,255,255,0.1);
            border-radius: .6rem;
        }
        label {
            font-size: .9rem;
            opacity: .8;
        }
        .btn-volver {
            display: inline-block;
            margin-top: 1.5rem;
            padding: .6rem 1rem;
            background: #007bff;
            border-radius: .5rem;
            color: white;
            text-decoration: none;
            font-size: .9rem;
        }
    </style>
</head>

<body>

<aside class="nav-top">
    <nav>
        <ul>
            <li style="background: white;"><img class="icon" src="/assets/img/logo.svg"></li>
            <li><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
            <li><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
            <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>
            <li class="seleccionada"><a href="matriculas.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>
        </ul>
        <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>

<main>
    <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
    <h2>Datos Completos de Matrícula de <?= htmlspecialchars($d['nombre_estudiante'] . " " . $d['apellidos_estudiante']) ?></h2>
    <div class="card">
        

        <div class="grid">

            <div class="campo">
                <label>ID</label>
                <div><?= $d['id'] ?></div>
            </div>

            <div class="campo">
                <label>Nombre Estudiante</label>
                <div><?= htmlspecialchars($d['nombre_estudiante'] . " " . $d['apellidos_estudiante']) ?></div>
            </div>

            <div class="campo">
                <label>RUT del Estudiante</label>
                <div><?= htmlspecialchars($d['rut_estudiante']) ?></div>
            </div>

            <div class="campo">
                <label>Fecha de Nacimiento</label>
                <div><?= htmlspecialchars($d['fecha_nacimiento']) ?></div>
            </div>

            <div class="campo">
                <label>N° Serie Carnet</label>
                <div><?= htmlspecialchars($d['serie_carnet_estudiante']) ?></div>
            </div>

            <div class="campo">
                <label><?= ($d['estado'] === 'Activa') ? 'Curso Actual' : 'Curso Preferido' ?></label>
                <div>
                    <?= $d['nivel'] ? $d['nivel'] . $d['letra'] : "No asignado" ?>
                </div>
            </div>

            <div class="campo">
                <label>Jornada Preferida</label>
                <div><?= htmlspecialchars($d['jornada_preferida']) ?></div>
            </div>

            <div class="campo">
                <label>Telefono del estudiante</label>
                <div><?= htmlspecialchars($d['telefono_estudiante']) ?></div>
            </div>

            <div class="campo">
                <label>Nombre Apoderado</label>
                <div><?= htmlspecialchars($d['nombre_apoderado']) ?></div>
            </div>
            <div class="campo">
                <label>Rut Apoderado</label>
                <div><?= htmlspecialchars($d['rut_apoderado']) ?></div>
            </div>


            <div class="campo">
                <label>Dirección Apoderado</label>
                <div><?= htmlspecialchars($d['direccion_apoderado']) ?></div>
            </div>

            

            <div class="campo">
                <label>Fecha Registro</label>
                <div><?= htmlspecialchars($d['fecha_registro']) ?></div>
            </div>

        </div>

        <a href="matriculas.php" class="btn-volver">⬅ Volver</a>
    </div>

</main>
<aside class="nav-bottom">
    <nav>
        <ul>
            <li style="background: white;"><img class="icon" src="/assets/img/logo.svg"></li>
            <li><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
            <li><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
            <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>
            <li class="seleccionada"><a href="matriculas.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>
        </ul>
        <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>

</body>
</html>
