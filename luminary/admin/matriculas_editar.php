<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// ======================
// 1️⃣ Validar ID recibido
// ======================
if (!isset($_GET['id'])) {
    exit("ID de matrícula no proporcionado.");
}

$id = intval($_GET['id']);

// ======================
// 2️⃣ Obtener matrícula
// ======================
$query = "
    SELECT * FROM matriculas 
    WHERE id = ?
    LIMIT 1
";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    exit("Matrícula no encontrada.");
}

$matricula = $resultado->fetch_assoc();

// ======================
// 3️⃣ Obtener cursos para el select
// ======================
$cursos = $conexion->query("SELECT id, nivel, letra FROM cursos ORDER BY nivel, letra");


// ======================
// 4️⃣ Guardar cambios (POST)
// ======================
// ======================
// 4️⃣ Guardar cambios (POST)
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $rut = $_POST['rut'];
    $serie = $_POST['serie'];
    $curso = $_POST['curso_preferido'];
    $jornada = $_POST['jornada'];
    $telefono = $_POST['telefono'];
    $apoderado = $_POST['apoderado'];
    $rut_apoderado = $_POST['rut_apoderado'];
    $direccion_apoderado = $_POST['direccion_apoderado'];

    $update = "
        UPDATE matriculas SET 
            nombre_estudiante = ?,
            apellidos_estudiante = ?,
            fecha_nacimiento = ?,
            rut_estudiante = ?,
            serie_carnet_estudiante = ?,
            curso_preferido = ?,
            jornada_preferida = ?,
            telefono_estudiante = ?,
            nombre_apoderado = ?,
            rut_apoderado = ?,
            direccion_apoderado = ?
        WHERE id = ?
    ";

    $stmt2 = $conexion->prepare($update);
    $stmt2->bind_param(
        "sssssiissssi",
        $nombre,
        $apellidos,
        $fecha_nacimiento,
        $rut,
        $serie,
        $curso,
        $jornada,
        $telefono,
        $apoderado,
        $rut_apoderado,
        $direccion_apoderado,
        $id
    );

    if ($stmt2->execute()) {
        header("Location: matriculas.php?ok=1");
        exit;
    } else {
        $error = "Error al actualizar la matrícula: " . $conexion->error;
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Matrícula</title>
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
        input {
            width: 100%;
            padding: .5rem;
            font-family: Outfit, sans-serif;
            border-radius: .5rem;
            border: 1px solid #ccc;
            background-color: rgba(255,255,255,0.5);
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
    <h2>Editar Matrícula de <?= htmlspecialchars($matricula['nombre_estudiante'] . " " . $matricula['apellidos_estudiante']) ?></h2>
    <div class="card">
        

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">

            <div class="grid">
                <div class="campo">
                    <label>Nombre estudiante</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($matricula['nombre_estudiante']) ?>" required>
                </div>

                <div class="campo">
                    <label>Apellidos estudiante</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($matricula['apellidos_estudiante']) ?>" required>
                </div>

                <div class="campo">
                    <label>RUT del Estudiante</label>
                    <input type="text" name="rut" value="<?= htmlspecialchars($matricula['rut_estudiante']) ?>" required>
                </div>

                <div class="campo">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($matricula['fecha_nacimiento']) ?>" required>
                </div>

                <div class="campo">
                    <label>N° Serie Carnet</label>
                    <input type="text" name="serie" value="<?= htmlspecialchars($matricula['serie_carnet_estudiante']) ?>" required>
                </div>

                <div class="campo">
                    <label>Curso Preferido</label>
                    <select name="curso_preferido">
                        <option value="">Sin curso preferido</option>

                    <?php while ($c = $cursos->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= ($matricula['curso_preferido'] == $c['id']) ? "selected" : "" ?>>
                            <?= $c['nivel'] . $c['letra'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                </div>

                <div class="campo">
                    <label>Jornada Preferida</label>
                    <select name="jornada">
                        <option value="Mañana" <?= $matricula['jornada_preferida'] === "Mañana" ? "selected" : "" ?>>Mañana</option>
                        <option value="Tarde" <?= $matricula['jornada_preferida'] === "Tarde" ? "selected" : "" ?>>Tarde</option>
                        <option value="Noche" <?= $matricula['jornada_preferida'] === "Noche" ? "selected" : "" ?>>Noche</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Telefono del Estudiante</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($matricula['telefono_estudiante']) ?>" required>
                </div>

                <div class="campo">
                    <label>Nombre Apoderado</label>
                    <input type="text" name="apoderado" value="<?= htmlspecialchars($matricula['nombre_apoderado']) ?>" required>
                </div>
                <div class="campo">
                    <label>Rut Apoderado</label>
                    <input type="text" name="rut_apoderado" value="<?= htmlspecialchars($matricula['rut_apoderado']) ?>" required>
                </div>
                <div class="campo">
                    <label>Dirección Apoderado</label>
                    <input type="text" name="direccion_apoderado" value="<?= htmlspecialchars($matricula['direccion_apoderado']) ?>" required>
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a class="btn-volver" href="matriculas.php">⬅ Volver</a>
                <button style="margin-top: 1.5rem;" type="submit">Guardar Cambios</button>
            </div>
            
        </form>
        
        
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
