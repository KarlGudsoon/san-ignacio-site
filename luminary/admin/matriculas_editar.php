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
$estudiante_id = $matricula['estudiante_id'];

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
    $etnia_estudiante = $_POST['etnia_estudiante'];
    $direccion_estudiante = $_POST['direccion_estudiante'];
    $correo_estudiante = $_POST['correo_estudiante'];
    $curso = $_POST['curso_preferido'];
    $jornada = $_POST['jornada_preferida'];
    $telefono = $_POST['telefono'];
    $hijos_estudiante = $_POST['hijos_estudiante'];
    $situacion_especial_estudiante = $_POST['situacion_especial_estudiante'];
    $programa_estudiante = $_POST['programa_estudiante'];
    $apoderado = $_POST['apoderado'];
    $rut_apoderado = $_POST['rut_apoderado'];
    $parentezco = $_POST['parentezco'];
    $direccion_apoderado = $_POST['direccion_apoderado'];
    $telefono_apoderado = $_POST['telefono_apoderado'];
    $situacion_especial_apoderado = $_POST['situacion_especial_apoderado'];

    $update = "
        UPDATE matriculas SET 
            nombre_estudiante = ?,
            apellidos_estudiante = ?,
            fecha_nacimiento = ?,
            rut_estudiante = ?,
            serie_carnet_estudiante = ?,
            etnia_estudiante = ?,
            direccion_estudiante = ?,
            correo_estudiante = ?,
            hijos_estudiante = ?,
            situacion_especial_estudiante = ?,
            programa_estudiante = ?,
            curso_preferido = ?,
            jornada_preferida = ?,
            telefono_estudiante = ?,
            nombre_apoderado = ?,
            rut_apoderado = ?,
            parentezco_apoderado = ?,
            direccion_apoderado = ?,
            telefono_apoderado = ?,
            situacion_especial_apoderado = ?
        WHERE id = ?
    ";

    $stmt2 = $conexion->prepare($update);
    $stmt2->bind_param(
        "ssssssssssiissssssssi",
        $nombre,
        $apellidos,
        $fecha_nacimiento,
        $rut,
        $serie,
        $etnia_estudiante,
        $direccion_estudiante,
        $correo_estudiante,
        $hijos_estudiante,
        $situacion_especial_estudiante,
        $programa_estudiante,
        $curso,
        $jornada,   // ahora es string ✔
        $telefono,
        $apoderado,
        $rut_apoderado,
        $parentezco_apoderado,
        $direccion_apoderado,
        $telefono_apoderado,
        $situacion_especial_apoderado,
        $id
    );

    if ($stmt2->execute()) {
        $estudiante_id = $matricula['estudiante_id'];
        // SOLO ejecutar si la matrícula ya está activada y tiene estudiante
        if (!empty($estudiante_id)) {

            // 1. Actualizar el curso del estudiante
            $stmt = $conexion->prepare("
                UPDATE estudiantes 
                SET curso_id = ? 
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $curso, $estudiante_id);
            $stmt->execute();
            $stmt->close();

            // 2. Actualizar curso_preferido en matriculas (por seguridad extra)
            $stmt = $conexion->prepare("
                UPDATE matriculas 
                SET curso_preferido = ? 
                WHERE estudiante_id = ?
            ");
            $stmt->bind_param("ii", $curso, $estudiante_id);
            $stmt->execute();
            $stmt->close();

            // 3. Obtener asignaturas habilitadas del nuevo curso
            $stmt = $conexion->prepare("
                SELECT asignatura_id 
                FROM curso_asignatura 
                WHERE curso_id = ?
            ");
            $stmt->bind_param("i", $curso);
            $stmt->execute();
            $result = $stmt->get_result();
            $asignaturas_habilitadas = [];

            while ($row = $result->fetch_assoc()) {
                $asignaturas_habilitadas[] = $row['asignatura_id'];
            }
            $stmt->close();

            // 4. Eliminar notas de asignaturas que NO pertenezcan al curso nuevo
            if (!empty($asignaturas_habilitadas)) {
                $placeholders = implode(',', array_fill(0, count($asignaturas_habilitadas), '?'));
                $params = array_merge([$estudiante_id], $asignaturas_habilitadas);
                $types = str_repeat('i', count($params));

                $query = "
                    DELETE FROM notas 
                    WHERE estudiante_id = ? 
                    AND asignatura_id NOT IN ($placeholders)
                ";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $stmt->close();
            }

            // 5. Actualizar profesor_id en notas según curso y asignaturas
            foreach ($asignaturas_habilitadas as $asignatura_id) {

                // obtener profesor asignado
                $stmt = $conexion->prepare("
                    SELECT profesor_id 
                    FROM curso_profesor 
                    WHERE curso_id = ? AND asignatura_id = ?
                ");
                $stmt->bind_param("ii", $curso, $asignatura_id);
                $stmt->execute();
                $res = $stmt->get_result();
                $profesor = $res->fetch_assoc();
                $stmt->close();

                if ($profesor) {
                    $profesor_id = $profesor['profesor_id'];

                    // actualizar notas existentes
                    $stmt = $conexion->prepare("
                        UPDATE notas 
                        SET profesor_id = ? 
                        WHERE estudiante_id = ? AND asignatura_id = ?
                    ");
                    $stmt->bind_param("iii", $profesor_id, $estudiante_id, $asignatura_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
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
<?php
include "components/aside.php"
?>
<main>
    <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
    <h2>Editar Matrícula de <?= htmlspecialchars($matricula['nombre_estudiante'] . " " . $matricula['apellidos_estudiante']) ?></h2>
    <div class="card">
        

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>
        <?php
        $es_activa = ($matricula['estado'] === 'Activa');
        $label_curso = $es_activa 
            ? "Curso Actual" 
            : "Curso Preferido";
        $atributo_disabled = $es_activa 
            ? 'disabled' 
            : '';
        ?>
        <form method="POST">

            <div class="grid">
                <div class="campo">
                    <label>Nombre estudiante</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($matricula['nombre_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Apellidos estudiante</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($matricula['apellidos_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>RUT del Estudiante</label>
                    <input type="text" name="rut" value="<?= htmlspecialchars($matricula['rut_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($matricula['fecha_nacimiento']) ?>">
                </div>

                <div class="campo">
                    <label>N° Serie Carnet</label>
                    <input type="text" name="serie" value="<?= htmlspecialchars($matricula['serie_carnet_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Etnia Estudiante</label>
                    <input type="text" name="etnia_estudiante" value="<?= htmlspecialchars($matricula['etnia_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Dirección Estudiante</label>
                    <input type="text" name="direccion_estudiante" value="<?= htmlspecialchars($matricula['direccion_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Correo Estudiante</label>
                    <input type="text" name="correo_estudiante" value="<?= htmlspecialchars($matricula['correo_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label><?= $label_curso ?></label>
                    <select name="curso_preferido" <?= $atributo_disabled ?>>
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
                    <select name="jornada_preferida">
                        <option value="" selected>Sin preferencia</option>
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Telefono Estudiante</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($matricula['telefono_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Hijos Estudiante</label>
                    <input type="text" name="hijos_estudiante" value="<?= htmlspecialchars($matricula['hijos_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Situación Especial Estudiante</label>
                    <input type="text" name="hijos_estudiante" value="<?= htmlspecialchars($matricula['situacion_especial_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Programa Especial Estudiante</label>
                    <input type="text" name="programa_estudiante" value="<?= htmlspecialchars($matricula['programa_estudiante']) ?>">
                </div>

                <div class="campo">
                    <label>Nombre Apoderado</label>
                    <input type="text" name="apoderado" value="<?= htmlspecialchars($matricula['nombre_apoderado']) ?>">
                </div>
                <div class="campo">
                    <label>Rut Apoderado</label>
                    <input type="text" name="rut_apoderado" value="<?= htmlspecialchars($matricula['rut_apoderado']) ?>">
                </div>

                <div class="campo">
                    <label>Parentezco</label>
                    <input type="text" name="parentezco_apoderado" value="<?= htmlspecialchars($matricula['parentezco_apoderado']) ?>">
                </div>

                <div class="campo">
                    <label>Dirección Apoderado</label>
                    <input type="text" name="direccion_apoderado" value="<?= htmlspecialchars($matricula['direccion_apoderado']) ?>">
                </div>

                <div class="campo">
                    <label>Teléfono Apoderado</label>
                    <input type="text" name="telefono_apoderado" value="<?= htmlspecialchars($matricula['telefono_apoderado']) ?>">
                </div>
                
                <div class="campo">
                    <label>Situación Especial Apoderado</label>
                    <input type="text" name="situacion_especial_apoderado" value="<?= htmlspecialchars($matricula['situacion_especial_apoderado']) ?>">
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a class="btn-volver" href="matriculas.php">⬅ Volver</a>
                <button style="margin-top: 1.5rem;" type="submit">Guardar Cambios</button>
            </div>
            
        </form>
        
        
    </div>
</main>
<?php
include "components/aside_bottom.php"
?>
</body>
</html>
