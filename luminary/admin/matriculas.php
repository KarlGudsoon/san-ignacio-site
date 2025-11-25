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
            padding: 0;
            background: rgba(255,255,255,0.1);
            border-radius: 1rem;
            margin: 1rem;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            overflow-x: auto;
            flex: 1;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            margin: 0;
            border-radius: 0;
        }
        thead {
            position: sticky;
            top: 0;
        }
        th, td {
            border-radius: 0;
            box-shadow: none;
            border-bottom: 1px solid white;
        }
        tr td {
            border-bottom: 1px solid #ffffffff;
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
        #formImportar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
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
        <p>A continuación puedes ver todas las matrículas ingresadas en el sistema. Puedes importar la nomina de estudiantes matriculados del SIGE, descarga la nomina de estudiantes en formato .TXT e importalo desde aquí.</p>

        <!-- 👇 BOTÓN AGREGAR MATRÍCULA -->
        <form id="formImportar" onsubmit="procesarTXT(event)">
            <input class="archivo" type="file" name="archivo" id="txt" accept=".txt" required>
            <button>Importar matrícula</button>
        </form>
    </div>
    <h2>Matrículas recibidas desde el formulario</h2>
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
    <h2>Matrículas activas</h2>
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


<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>

function procesarTXT(event) {
    event.preventDefault(); // Evitar recargar la página

    const fileInput = document.getElementById('txt');
    const file = fileInput.files[0];

    if (!file) {
        alert("Debes seleccionar un archivo TXT.");
        return;
    }

    let reader = new FileReader();

    reader.onload = function(e) {
        // 1️⃣ Leer como binario
        let bytes = new Uint8Array(e.target.result);

        // 2️⃣ Convertir Windows-1252 → UTF-8
        let texto = new TextDecoder("windows-1252").decode(bytes);

        // 3️⃣ Procesar TXT
        let lineas = texto.split(/\r?\n/);
        let headers = lineas[0].split(";");

        const columnas = {
            "run_alumno": "rut",
            "dgv_alumno": "codigo_verificador",
            "nombre_alumno": "nombres",
            "ape_paterno_alumno": "apellido_paterno",
            "ape_materno_alumno": "apellido_materno",
            "fecha_nacimiento": "fecha_nacimiento",
            "direccion_alumno": "direccion",
            "email_alumno": "email",
            "telefono_alumno": "telefono",
            "codigo_curso": "codigo_curso",
            "letra_curso": "letra_curso"
        };

        let resultado = [];

        for (let i = 1; i < lineas.length; i++) {
            if (!lineas[i].trim()) continue;

            let valores = lineas[i].split(";");
            let fila = {};

            for (let j = 0; j < headers.length; j++) {
                if (columnas[headers[j]]) {
                    fila[columnas[headers[j]]] = valores[j] || "";
                }
            }

            resultado.push(fila);
        }

        // 4️⃣ Enviar a PHP
        fetch("importar_matriculas.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(resultado)
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            fileInput.value = ""; // Limpiar input
        })
        .catch(err => alert("Error: " + err));
    };

    reader.readAsArrayBuffer(file);
}





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
