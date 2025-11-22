<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

$profesor_id = $_SESSION['id'];

// Obtener todos los cursos y asignaturas del profesor
$cursos_asignaturas = $conexion->query("
    SELECT cp.curso_id, cp.asignatura_id, c.nivel, c.letra, a.nombre AS asignatura
    FROM curso_profesor cp
    JOIN cursos c ON cp.curso_id = c.id
    JOIN asignaturas a ON cp.asignatura_id = a.id
    WHERE cp.profesor_id = $profesor_id
    ORDER BY c.nivel, c.letra, a.nombre
");

$cursos_jefatura = $conexion->query("
    SELECT c.id, c.nivel, c.letra
    FROM cursos c
    WHERE c.profesor_jefe_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

$primerCursoJefatura = $cursos_jefatura->fetch_assoc();
$cursos_jefatura->data_seek(0);


// Verificar si el profesor tiene cursos asignados
if ($cursos_asignaturas->num_rows == 0) {
    echo "No tienes cursos asignados.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Profesor - Todas las Notas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/global.css">   
    <link rel="stylesheet" href="style.css">   
    <style>
        .curso-container {
            margin-bottom: 30px;
            border-radius: 5px;
            width: fit-content;
        }
        .curso-title {
            background-color: var(--secondarycolor);
            padding: 1rem;
            margin-bottom: 15px;
            color: white;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.35);
        }
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

        .estudiantes {
            background-color: #035bad;
            text-align: left;
            color: white;
            border-radius: 0.5rem;
        }

        table input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            text-align: center;
            font-family: Outfit, sans-serif;
        }

        table input:focus {
            outline: none;
            border-color: #035bad;
            box-shadow: 0 0 5px rgba(3, 91, 173, 0.5);
        }

        .promedio {
            font-weight: bold;
            color: white;
            background-color: #035bad;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 5px;
            border-radius: 0.5rem;
            height: fit-content;
        }

        button {
            background-color: #03ad77;
            color: white;
            padding: 10px 20px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            font-family: Outfit, sans-serif;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.35), inset 0 0 2px rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li class="seleccionada"><a href="notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
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
        <a class="volver" href="editor.php"><img src="/assets/icons/arrow.svg"></a>
        <h1>Todas las notas de tus cursos</h1>

        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 5px;">
        <?= $_SESSION['mensaje_exito'] ?>
            </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        
        
        <div class="selector-curso">
            <label for="selector-curso">Selecciona un curso para visualizar las notas:</label>
            <select id="selector-curso">
                <option value="">-- Selecciona --</option>
                <?php
                $indice = 0;
                $cursos_asignaturas->data_seek(0); // Reiniciar puntero de resultados
                while ($curso_asignatura = $cursos_asignaturas->fetch_assoc()):
                    $id = "curso_" . $indice;
                    $nombre = $curso_asignatura['nivel'] . $curso_asignatura['letra'] . " - " . $curso_asignatura['asignatura'];
                ?>
                    <option value="<?= $id ?>"><?= $nombre ?></option>
                <?php $indice++; endwhile; ?>
            </select>

            <?php
            $indice = 0;
            $cursos_asignaturas->data_seek(0); // Reiniciar puntero de resultados nuevamente

            while ($curso_asignatura = $cursos_asignaturas->fetch_assoc()):
                $curso_id = $curso_asignatura['curso_id'];
                $asignatura_id = $curso_asignatura['asignatura_id'];
                $form_id = "curso_" . $indice;

                $estudiantes = $conexion->query("
                    SELECT * FROM estudiantes 
                    WHERE curso_id = $curso_id 
                    ORDER BY nombre
                ");
            ?>
        </div>

        

        
        
        <div class="curso-container" id="<?= $form_id ?>" style="display: none;">
            <div class="curso-title">
                <h2><?= $curso_asignatura['nivel'] . $curso_asignatura['letra'] ?> - <?= $curso_asignatura['asignatura'] ?></h2>
            </div>
            <form method="POST" action="guardar_notas.php">
                <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
                <input type="hidden" name="asignatura_id" value="<?= $asignatura_id ?>">

                <table>
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
                            WHERE estudiante_id = {$e['id']} 
                            AND asignatura_id = $asignatura_id 
                            AND profesor_id = $profesor_id
                        ")->fetch_assoc();
                    ?>
                    <tr>
                        <td  class="estudiantes"><?= $e['nombre'] ?></td>
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
                <button type="submit" class="guardar-btn">Guardar cambios</button>
            </form>
        </div>
        <?php $indice++; endwhile; ?>
    </main>
    <aside class="nav-bottom">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li class="seleccionada"><a href="notas.php"><img class="icon" src="/assets/icons/grade.svg"></a></li>
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

<script>
document.getElementById("selector-curso").addEventListener("change", function () {
    const selectedId = this.value;
    const containers = document.querySelectorAll(".curso-container");

    containers.forEach(container => {
        container.style.display = "none";
        container.style.opacity = "0"; 
        container.style.transform = "translateY(20px)"; // Desplazamiento hacia abajo
        container.style.transition = "0.5s ease"; // Añadir transición
    });

    if (selectedId) {
        document.getElementById(selectedId).style.display = "block";
        

        setTimeout (() => {
            document.getElementById(selectedId).style.opacity = "1";
            document.getElementById(selectedId).style.transform = "translateY(0px)";
            document.getElementById(selectedId).style.scale = "1";
        }, 100);
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

</script>
