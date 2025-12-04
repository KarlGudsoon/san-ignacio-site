<?php
include '../conexion.php';
session_start();

if ($_SESSION['rol'] !== 'editor') {
    header("Location: ../index.html");
    exit;
}

$curso_id = $_GET['curso_id'];
$asignatura_id = $_GET['asignatura_id'];
$profesor_id = $_SESSION['id'];

// Verificar que este profesor está asignado a ese curso y asignatura
$verificar = $conexion->query("
    SELECT * FROM curso_profesor
    WHERE curso_id = $curso_id AND asignatura_id = $asignatura_id AND profesor_id = $profesor_id
");
if ($verificar->num_rows == 0) {
    echo "No tienes acceso a este curso/asignatura.";
    exit;
}

// Obtener estudiantes
$estudiantes = $conexion->query("
    SELECT e.*, m.nombre_estudiante, m.apellidos_estudiante, m.rut_estudiante
    FROM estudiantes e
    INNER JOIN matriculas m ON e.matricula_id = m.id
    WHERE e.curso_id = $curso_id
");

$cursos_jefatura = $conexion->query("
    SELECT c.id, c.nivel, c.letra
    FROM cursos c
    WHERE c.profesor_jefe_id = $profesor_id
    ORDER BY c.nivel, c.letra
");

$primerCursoJefatura = $cursos_jefatura->fetch_assoc();
$cursos_jefatura->data_seek(0);


// Obtener nombre del curso y asignatura
$info = $conexion->query("
    SELECT c.nivel, c.letra, a.nombre AS asignatura
    FROM cursos c, asignaturas a
    WHERE c.id = $curso_id AND a.id = $asignatura_id
")->fetch_assoc();
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
    <div style="text-align: right;">
        
    </div>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="editor.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
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
        <a class="volver" href="editor.php"><img src="/assets/icons/arrow.svg"></a>
        <h2>Curso: <?= $info['nivel'] . ' Nivel ' . $info['letra'] ?> - <?= $info['asignatura'] ?></h2>

        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 5px;">
        <?= $_SESSION['mensaje_exito'] ?>
            </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <form method="POST" action="guardar_notas_curso.php">
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



