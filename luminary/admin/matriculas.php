<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// ===== Obtener matrículas =====
// MATRÍCULAS PENDIENTES
$pendientes = $conexion->query("
    SELECT m.*, c.nivel, c.letra
    FROM matriculas m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    WHERE m.estado = 'Pendiente'
    ORDER BY m.id DESC
");

// MATRÍCULAS ACTIVAS
$activas = $conexion->query("
    SELECT m.*, c.nivel, c.letra
    FROM matriculas m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    WHERE m.estado = 'Activa'
    ORDER BY m.id DESC
");
function calcularEdad($fecha_nacimiento) {
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Directivo - Matrículas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../public/global.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .contenedor-tabla {
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            margin: 1rem;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            margin: 0;
            border-radius: 0;
        }
        th, td {
            border-radius: 0;
            box-shadow: none;
        }
        .btn {
            padding: .4rem .7rem;
            border-radius: .4rem;
            font-size: .9rem;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-ver {
            background: #17a2b8;
        }
        .btn-agregar {
            background: #28a745;
        }
        .btn-editar {
            background: #007bff;
        }
        .btn-eliminar {
            background: #dc3545;
        }
        td:last-child {
            display: flex;
            gap: .4rem;
            justify-content: center;
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
    <div class="contenedor-informacion">
        <h1>Matrículas Registradas</h1>
        <p>A continuación puedes ver todas las matrículas ingresadas en el sistema.</p>

        <!-- 👇 BOTÓN AGREGAR MATRÍCULA -->
        <a href="matriculas_agregar.php" class="btn btn-agregar">+ Agregar Matrícula</a>
    </div>

    <div class="contenedor-tabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>RUT</th>
                    <th>Edad</th>
                    <th>Curso Preferido</th>
                    <th>Jornada Preferida</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($pendientes->num_rows === 0): ?>
                <tr><td colspan="7" style="text-align:center;">No hay matrículas pendientes</td></tr>
            <?php else: ?>
                <?php while ($row = $pendientes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_estudiante'] ." ". $row['apellidos_estudiante']) ?></td>
                    <td><?= htmlspecialchars($row['rut_estudiante']) ?></td>
                    <td><?= calcularEdad($row['fecha_nacimiento']) ?> años</td>

                    <td><?= $row['nivel'] ? $row['nivel'].$row['letra'] : "No asignado" ?></td>

                    <td><?= htmlspecialchars($row['jornada_preferida']) ?></td>

                    <td><strong style="color:#dc3545;"><?= $row['estado'] ?></strong></td>

                    <td>
                        <a class="btn btn-ver" href="matriculas_ver.php?id=<?= $row['id'] ?>">Ver</a>
                        <a class="btn btn-editar" href="matriculas_editar.php?id=<?= $row['id'] ?>">Editar</a>
                        <a class="btn btn-eliminar" onclick="eliminarMatricula(<?= $row['id'] ?>)">Eliminar</a>
                        <a class="btn btn-agregar" href="matriculas_activar.php?id=<?= $row['id'] ?>">+</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="contenedor-tabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>RUT</th>
                    <th>Curso Actual</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($activas->num_rows === 0): ?>
                <tr><td colspan="6" style="text-align:center;">No hay matrículas activas</td></tr>
            <?php else: ?>
                <?php while ($row = $activas->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_estudiante'] ." ". $row['apellidos_estudiante']) ?></td>
                    <td><?= htmlspecialchars($row['rut_estudiante']) ?></td>

                    <td><?= $row['nivel'] ? $row['nivel'].$row['letra'] : "No asignado" ?></td>

                    <td><strong style="color:#28a745;"><?= $row['estado'] ?></strong></td>

                    <td>
                        <a class="btn btn-ver" href="matriculas_ver.php?id=<?= $row['id'] ?>">Ver</a>
                        <a class="btn btn-editar" href="matriculas_editar.php?id=<?= $row['id'] ?>">Editar</a>
                        <a class="btn btn-eliminar" onclick="eliminarMatricula(<?= $row['id'] ?>)">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
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

function eliminarMatricula(id) {
    if (!confirm("¿Seguro que deseas eliminar esta matrícula?")) return;

    fetch("matriculas_eliminar.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
    })
    .then(r => r.text())
    .then(resp => {
        if (resp.trim() === "OK") {
            location.reload();
        } else {
            alert("Error: " + resp);
        }
    });
}
</script>
