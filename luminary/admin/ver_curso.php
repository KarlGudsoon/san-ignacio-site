<?php
include '../conexion.php';
session_start();

// Verificar permisos de admin
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// Obtener ID del curso
$curso_id = $_GET['id'] ?? null;
if (!$curso_id || !is_numeric($curso_id)) {
    echo "Curso no válido.";
    exit;
}

// Obtener información del curso
$stmt = $conexion->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    echo "Curso no encontrado.";
    exit;
}

// Procesar eliminación de estudiante
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $estudiante_id = $_GET['eliminar'];
    
    try {
        $conexion->begin_transaction();
        
        // 1. Eliminar las notas del estudiante
        $stmt = $conexion->prepare("DELETE FROM notas WHERE estudiante_id = ?");
        $stmt->bind_param("i", $estudiante_id);
        $stmt->execute();
        $stmt->close();
        
        // 2. Eliminar al estudiante
        $stmt = $conexion->prepare("DELETE FROM estudiantes WHERE id = ? AND curso_id = ?");
        $stmt->bind_param("ii", $estudiante_id, $curso_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $conexion->commit();
            $_SESSION['mensaje'] = "Estudiante eliminado correctamente";
        } else {
            $conexion->rollback();
            $_SESSION['error'] = "No se pudo eliminar el estudiante";
        }
        
        $stmt->close();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
        
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['error'] = "Error al eliminar: " . $e->getMessage();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignaturas_habilitadas'])) {
    $asignaturas_seleccionadas = $_POST['asignaturas_habilitadas'];

    try {
        // Eliminar todas las asignaciones actuales del curso
        $stmt = $conexion->prepare("DELETE FROM curso_asignatura WHERE curso_id = ?");
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        $stmt->close();

        // Insertar las nuevas asignaturas habilitadas
        $stmt = $conexion->prepare("INSERT INTO curso_asignatura (curso_id, asignatura_id) VALUES (?, ?)");
        foreach ($asignaturas_seleccionadas as $asig_id) {
            $asig_id = (int)$asig_id;
            $stmt->bind_param("ii", $curso_id, $asig_id);
            $stmt->execute();
        }
        $stmt->close();

        $_SESSION['mensaje'] = "Asignaturas actualizadas correctamente";
        header("Location: ver_curso.php?id=$curso_id");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error al actualizar asignaturas: " . $e->getMessage();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
    }
}

// Procesar asignación de profesor jefe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profesor_jefe'])) {
    $profesor_jefe_id = $_POST['profesor_jefe'];
    
    try {
        $stmt = $conexion->prepare("UPDATE cursos SET profesor_jefe_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $profesor_jefe_id, $curso_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = "Profesor jefe asignado correctamente";
        } else {
            $_SESSION['error'] = "No se realizaron cambios en la asignación del profesor jefe";
        }
        
        $stmt->close();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al asignar profesor jefe: " . $e->getMessage();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
    }
}

// Traspasar estudiante de otro curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['traspasar_estudiante'])) {
    $estudiante_id = (int)$_POST['estudiante_id'];

    try {
        // 1. Actualizar el curso del estudiante
        $stmt = $conexion->prepare("UPDATE estudiantes SET curso_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $curso_id, $estudiante_id);
        $stmt->execute();
        $stmt->close();

        // 2. Obtener asignaturas habilitadas del curso destino
        $stmt = $conexion->prepare("SELECT asignatura_id FROM curso_asignatura WHERE curso_id = ?");
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $asignaturas_habilitadas = [];
        while ($row = $result->fetch_assoc()) {
            $asignaturas_habilitadas[] = $row['asignatura_id'];
        }
        $stmt->close();

        // 3. Eliminar notas que no correspondan a las asignaturas del nuevo curso
        if (!empty($asignaturas_habilitadas)) {
            $placeholders = implode(',', array_fill(0, count($asignaturas_habilitadas), '?'));
            $params = array_merge([$estudiante_id], $asignaturas_habilitadas);
            $types = str_repeat('i', count($params));

            $stmt = $conexion->prepare("
                DELETE FROM notas 
                WHERE estudiante_id = ? 
                AND asignatura_id NOT IN ($placeholders)
            ");
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
        }

        // 4. Actualizar profesor_id en las notas del estudiante según el nuevo curso
        foreach ($asignaturas_habilitadas as $asignatura_id) {
            $stmt = $conexion->prepare("
                SELECT profesor_id 
                FROM curso_profesor 
                WHERE curso_id = ? AND asignatura_id = ?
            ");
            $stmt->bind_param("ii", $curso_id, $asignatura_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $profesor = $res->fetch_assoc();
            $stmt->close();

            if ($profesor) {
                $profesor_id = $profesor['profesor_id'];

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

        $_SESSION['mensaje'] = "Estudiante traspasado correctamente.";
        header("Location: ver_curso.php?id=$curso_id");
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al traspasar estudiante: " . $e->getMessage();
        header("Location: ver_curso.php?id=$curso_id");
        exit;
    }
}



// Obtener lista de estudiantes del curso
$stmt = $conexion->prepare("
    SELECT 
        e.id AS estudiante_id,
        m.nombre_estudiante,
        m.apellidos_estudiante,
        m.rut_estudiante
    FROM estudiantes e
    INNER JOIN matriculas m ON m.id = e.matricula_id
    WHERE e.curso_id = ?
    ORDER BY m.apellidos_estudiante ASC
");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$estudiantes = $stmt->get_result();
$stmt->close();


// Obtener profesor jefe actual
$profesor_jefe_actual = null;
if ($curso['profesor_jefe_id']) {
    $stmt = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $curso['profesor_jefe_id']);
    $stmt->execute();
    $profesor_jefe_actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Obtener todos los profesores disponibles para ser jefes (pueden ser todos los de rol 'editor')
$profesores_disponibles = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'editor' ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Directivo - Curso <?= htmlspecialchars($curso['nivel'] . $curso['letra']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/global.css">   
    <link rel="stylesheet" href="style.css">   

    <style>
        input {
            width: auto;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: Outfit, sans-serif;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .section {
            
            flex: 1;
            
        }

        section {
            display: flex;
            gap: 1rem;
        }

        .asignatura {
            color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
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
        }

        .curso a {
            text-decoration: none;
        }

        .curso:active {
            scale: 0.95
        }

        button {
            width: fit-content;
        }

        .contenedor-cursos {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 0.5rem;
            height: fit-content;
            flex-wrap: wrap;
        }

        .contenedor-cursos h2 {
            margin: 0;
            margin-right: 0.5rem;
        }

        .mensaje { color: green; padding: 10px; }
        .error { color: red; padding: 10px; }
        .form-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .form-section h3 { margin-top: 0; }

       

        

        .lista-estudiantes {
            height: 400px;
            flex: none;
            overflow: auto;
            border-radius: 2rem;
            margin-bottom: 1rem;
            position: relative;
            background-color: rgba(3, 91, 173, 0.2);
        }

        .lista-estudiantes::before {
            content: "";
            position: absolute;
            display: none;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg,rgb(0, 0, 0, 0.2) 50%, transparent);
        }

        .lista-estudiantes table {
            width: 100%;
            border: 0;
            border-radius: 0;
            margin: 0;
            background-color: transparent;
        }

        .estudiante {
            background-color: rgba(0, 0, 0, 0.35);
            padding: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
        }

        .eliminar {
            background-color: #eb3b3b;
            padding: 0.5rem;
            margin: 0;
        }

        .eliminar img {
            margin: 0;
        }

        main {
            position: relative;
        }

        .cerrar-contenedor {
            position: fixed;
            width: 35px;
            height: 35px;
            top: 0;
            right: 0;
            margin: 1rem;
            background-color: #035bad;
            border-radius: 100%;
            transition: 0.2s ease;
            cursor: pointer;
        }

        .cerrar-contenedor:hover {
            background-color: #eb3b3b;
            scale: 1.1;
        }

        .cerrar-contenedor img {
            height: 100%;
        }

        #widget-estudiante {
            position: fixed;
            bottom: 0;
            right: 0;
            background-color:rgb(235, 199, 23);
            width: 100px;
            height: 100px;
            margin: 2rem;
            border-radius: 100%;
            padding: 1rem;
            transition: 0.2s ease;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.35);
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #widget-estudiante:hover {
            background-color:rgb(224, 195, 47)
        }

        #widget-estudiante img {
            height: 100%;
            width: auto;
            filter: invert(1);
            margin: 0;
            opacity: 0.35;
        }

        .boton-estudiante {
            height: 100%;
            width: 100%;
        }

        .contenedor-boton-estudiante {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            width: auto;
            position: absolute;
            top: -90%;
            right: 0;
            gap: 0.5rem;
            opacity: 0;
            pointer-events: none;
            transition: 0.2s ease;
        }

        .contenedor-boton-estudiante.active {
            pointer-events: auto;
            opacity: 1;
        }

        .contenedor-boton-estudiante .boton {
            background-color: #dfc028;
            color: rgba(0, 0, 0, 0.5);
            width: max-content;
            display: flex;
            gap: 0.5rem;
        }

        .contenedor-notas-asignatura {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(15px);
            overflow: auto  ;
            z-index: 99999999;
        }
        
        .contenedor-notas-asignatura h3 {
            background-color: #035bad;
            border-radius: 1rem;
            padding: 1rem 1rem;
            color: white;
        }

        .contenedor-notas-asignatura h4 {
            background-color: #035bad;
            border-radius: 1rem;
            padding: 1rem 1rem;
            margin: 1rem 0 1rem 0;
            color: white;
            text-align: center;
            text-transform: uppercase;
        }

        .contenedor-notas-asignatura.active {
            display: flex;
            flex-direction: column;
        }

        .notas-asignatura table {
            background-color: #bfd0e1;
        }

        .botones-asignaturas {
            display: flex;
            gap: 1rem;
            width: fit-content;
            margin: 0 auto;
            margin-top: 2rem;
        }

        .notas-asignatura {
            display: none;
            top: 0;
            margin: 0 auto;
            margin-bottom: 2rem;
        }

        .promedio {
            width: 30px;
        }

        .contenedor-modal {
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            position: fixed;
            top: 0;
            left: 0;
            display: none;
            z-index: 9999999999;
        }

        .contenedor-modal .section {
            flex: none;
        }

        .contenedor-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contenedor-datos-notas {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .profesor-jefe {
            background-color: rgb(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.35);
        }

        .profesor-asignatura {
            padding: 0.5rem; 
            margin-bottom: 0.5rem; 
            flex-wrap:wrap; 
            display:flex; 
            gap: 5px;
            align-items:center; 
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
        }

        .profesor-asignatura select {
            border: 1px solid rgba(0, 0, 0, 0.5);
        }

        .abrir-asignatura {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .abrir-asignatura.activa::before {
            content: "";
            width: 5px;
            height: 100%;
            background-color: inherit;
            position: absolute;
            bottom: -100%;
            z-index: 1;
        }

        .abrir-asignatura.activa::before:hover {
            filter: brightness(100%);
        }

        @media (max-width: 600px) {
            #widget-estudiante {
                margin-bottom: 7rem;
                width: 50px;
                height: 50px;
            }

            .contenedor-boton-estudiante {
                top: -130%;
            }
        }

    </style>
</head>
<body>
    <aside class="nav-top">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li class="seleccionada"><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
                <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li>      
            </ul>
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>
    <main>
        <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
        <div class="contenedor-informacion"> 
            <h2>Curso: <span class="curso" style="font-weight: 300;"><?= htmlspecialchars($curso['nivel'] . " Nivel " . $curso['letra']) ?></span></h2>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="mensaje"><?= $_SESSION['mensaje'] ?></div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?> 
        </div>

        <div id="widget-estudiante">
            <div class="contenedor-boton-estudiante">
                <button class="boton" data-modal="contenedor-agregar-estudiante"><img src="/assets/icons/add.svg">Agregar estudiante</button>
                <button class="boton" data-modal="contenedor-traspasar-estudiante"><img src="/assets/icons/transfer.svg">Traspasar estudiante</button>
            </div>
            <div class="boton-estudiante"><img src="/assets/icons/add.svg"></div>
        </div>

        



        

        <div class="contenedor-principal">
            <div class="contenedor-interno-1" style="flex: 2;">
                <div class="lista-estudiantes">
                    <table cellpadding="8" cellspacing="0">
                        <thead style="position: sticky; top: 16px; background-color: #035bad;">
                            <tr>
                                <th>Nombre</th>
                                <th>RUT</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($est = $estudiantes->fetch_assoc()): ?>
                                <tr>
                                    <td class="nombre-estudiante">
                                        <a style="color: #035bad;"
                                        href="ficha_estudiante.php?id=<?= $est['estudiante_id'] ?>">
                                            <?= htmlspecialchars($est['nombre_estudiante'] . " " . $est['apellidos_estudiante']) ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($est['rut_estudiante']) ?>
                                    </td>

                                    <td style="display: flex; justify-content: center;">
                                        <button class="eliminar">
                                            <a href="ver_curso.php?id=<?= $curso_id ?>&eliminar=<?= $est['estudiante_id'] ?>"
                                            onclick="return confirm('¿Estás seguro de eliminar este estudiante?');">
                                                <img src="/assets/icons/delete.svg">
                                            </a>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="13"
                                        style="height: 1px; border-bottom: 1px solid #035bad; padding: 0; opacity: 0.2;">
                                    </td>
                                </tr>

                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="section" style="display: flex; flex-direction: column; justify-content:center;">
            
                    <h3 style="margin: 0;">Profesor Jefe Actual:</h3>
                    <?php if ($profesor_jefe_actual): ?>
                        <p class="profesor-jefe"><strong><?= htmlspecialchars($profesor_jefe_actual['nombre']) ?></strong></p>
                    <?php else: ?>
                        <p>No se ha asignado profesor jefe</p>
                    <?php endif; ?>

                    <form style="display:flex; flex-direction:column; justify-content:space-between; gap: 1rem" method="POST" action="ver_curso.php?id=<?= $curso_id ?>">
                        <div style="display:flex; flex-wrap:wrap; gap: 0.5rem; align-items: center;">
                        <label  for="profesor_jefe">Asignar nuevo profesor jefe:</label>
                        <select name="profesor_jefe" id="profesor_jefe" required>
                            <option value="">-- Seleccionar profesor jefe --</option>
                            <?php while ($profesor = $profesores_disponibles->fetch_assoc()): ?>
                                <option value="<?= $profesor['id'] ?>" <?= ($profesor_jefe_actual && $profesor['id'] == $profesor_jefe_actual['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($profesor['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        </div>
                        <button type="submit">Asignar Profesor Jefe</button>
                    </form>
                </div>
            </div>
            <div class="contenedor-interno-1">
                <div class="section" style="display:flex; flex-direction:column; justify-content: space-between;">
                    <div class="contenedor-datos-notas">
                        <div id="grafico-promedio"></div>
                        <div class="datos-promedio">
                            <p id="estudiantes-pendientes">Pendientes:</p>
                            <p id="estudiantes-sin-notas" style="width: 200px;"></p>        
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <button data-modal="contenedor-notas-asignatura"><img src="/assets/icons/notas.svg"> Notas de asignaturas</button>
                        <button style="padding: 10px;"><a target="_blank" href="../editor/descarga_notas_pdf_curso.php?curso_id=<?= $curso_id ?>"><img style="margin: 0;" src="/assets/icons/streamline--convert-pdf-2-solid.svg"></a></button>
                    </div>
                </div>
                <div class="section">
                    <h3>Habilitar asignaturas para este curso:</h3>
                    <form method="POST" action="ver_curso.php?id=<?= $curso_id ?>">

                        <?php
                        $todas_asignaturas = $conexion->query("SELECT * FROM asignaturas");

                        // Obtener las asignaturas ya habilitadas para el curso
                        $asignaturas_habilitadas_result = $conexion->query("SELECT asignatura_id FROM curso_asignatura WHERE curso_id = $curso_id");
                        $asignaturas_habilitadas = [];
                        while ($fila = $asignaturas_habilitadas_result->fetch_assoc()) {
                            $asignaturas_habilitadas[] = $fila['asignatura_id'];
                        }

                        while ($asig = $todas_asignaturas->fetch_assoc()):
                        ?>
                            <label>
                                <input type="checkbox" name="asignaturas_habilitadas[]" value="<?= $asig['id'] ?>"
                                    <?= in_array($asig['id'], $asignaturas_habilitadas) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($asig['nombre']) ?>
                            </label><br>
                        <?php endwhile; ?>

                        <br>
                        <button type="submit">Guardar asignaturas habilitadas</button>
                    </form>
                </div>
            </div>

            

            
            

            <div class="section">
                <h3>Asignar profesores por asignatura:</h3>
                <form method="POST" action="asignar_profesor.php">
                    <input type="hidden" name="curso_id" value="<?= $curso_id ?>">

                    <?php
                    // Obtener asignaturas
                    $stmt = $conexion->prepare("
                        SELECT a.* 
                        FROM asignaturas a
                        JOIN curso_asignatura ca ON a.id = ca.asignatura_id
                        WHERE ca.curso_id = ?
                        ORDER BY a.nombre
                    ");
                    $stmt->bind_param("i", $curso_id);
                    $stmt->execute();
                    $asignaturas = $stmt->get_result();

                    while ($asignatura = $asignaturas->fetch_assoc()):
                        $id_asignatura = $asignatura['id'];

                        // Obtener profesor asignado
                        $stmt = $conexion->prepare("SELECT profesor_id FROM curso_profesor WHERE curso_id = ? AND asignatura_id = ?");
                        $stmt->bind_param("ii", $curso_id, $id_asignatura);
                        $stmt->execute();
                        $asignado = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        $profesor_asignado_id = $asignado['profesor_id'] ?? null;

                        // Obtener profesores disponibles
                        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE rol = 'editor' AND asignatura = ?");
                        $stmt->bind_param("s", $asignatura['nombre']);
                        $stmt->execute();
                        $profesores = $stmt->get_result();
                        $stmt->close();
                    ?>
                    <div class="profesor-asignatura">
                    <label><?= htmlspecialchars($asignatura['nombre']) ?>:</label>
                    <select  name="profesores[<?= $id_asignatura ?>]">
                        <option value="">-- Seleccionar profesor --</option>
                        <?php while ($profe = $profesores->fetch_assoc()): ?>
                            <option value="<?= $profe['id'] ?>" <?= ($profe['id'] == $profesor_asignado_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($profe['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    </div>
                    <?php endwhile; ?>

                    <button type="submit">Asignar profesores</button>
                </form>
            </div>

            <div class="contenedor-modal modal" id="contenedor-agregar-estudiante">
                <div class="cerrar-contenedor"><img src="/assets/icons/close.svg"></div>
                <div class="section">
                    <h4>Agregar estudiante:</h4>
                    <form method="POST" action="agregar_estudiante.php">
                        <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
                        <input type="text" name="nombre" placeholder="Nombre completo" required><br><br>
                        <input type="text" name="rut" placeholder="RUT (12345678-9)" required><br><br>
                        <button type="submit">Agregar estudiante</button>
                    </form>
                </div>
            </div>

            <div class="contenedor-modal" id="contenedor-traspasar-estudiante">
                <div class="cerrar-contenedor"><img src="/assets/icons/close.svg"></div>
                <div class="section">
                    <h3>Traspasar estudiante desde otro curso</h3>
                    <form method="POST" action="ver_curso.php?id=<?= $curso_id ?>">
                        <label>Selecciona un curso:</label>
                        <select id="select-curso" name="curso_origen" required>
                            <option value="">-- Seleccionar curso --</option>
                            <?php
                            $cursos = $conexion->query("SELECT id, nivel, letra FROM cursos WHERE id != $curso_id ORDER BY nivel, letra");
                            while ($c = $cursos->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>"> <?= $c['nivel'] . ' Nivel ' . $c['letra'] ?> </option>
                            <?php endwhile; ?>
                        </select>

                        <br><br>

                        <label>Selecciona un estudiante:</label>
                        <select id="select-estudiante" name="estudiante_id" required disabled>
                            <option value="">-- Selecciona primero un curso --</option>
                        </select>

                        <br><br>
                        <button type="submit" name="traspasar_estudiante">Traspasar Estudiante</button>
                    </form>
                </div>
            </div>
            

            


            

            

            

            <div class="contenedor-notas-asignatura modal" id="contenedor-notas-asignatura">
            <div class="cerrar-contenedor"><img src="/assets/icons/close.svg"></div>

            <div class="botones-asignaturas">
                <?php
                // Repetimos la consulta para generar botones
                $stmt = $conexion->prepare("
                    SELECT a.id, a.nombre 
                    FROM asignaturas a 
                    JOIN curso_asignatura ca ON a.id = ca.asignatura_id 
                    WHERE ca.curso_id = ? 
                    ORDER BY a.nombre
                ");
                $stmt->bind_param("i", $curso_id);
                $stmt->execute();
                $asignaturas_boton = $stmt->get_result();
                $stmt->close();

                while ($asignatura = $asignaturas_boton->fetch_assoc()):
                ?>
                    <button class="abrir-asignatura asignatura" data-asignatura="<?= $asignatura['id'] ?>">
                        <?= htmlspecialchars($asignatura['nombre']) ?>
                    </button>
                <?php endwhile; ?>
            </div>
                <?php
                // Obtener asignaturas habilitadas para este curso
                $stmt = $conexion->prepare("
                    SELECT a.id, a.nombre 
                    FROM asignaturas a 
                    JOIN curso_asignatura ca ON a.id = ca.asignatura_id 
                    WHERE ca.curso_id = ? 
                    ORDER BY a.nombre
                ");
                $stmt->bind_param("i", $curso_id);
                $stmt->execute();
                $resultado_asignaturas = $stmt->get_result();
                $stmt->close();

                while ($asignatura = $resultado_asignaturas->fetch_assoc()):
                    $asignatura_id = $asignatura['id'];
                ?>
                    
                    <form class="notas-asignatura" method="POST" action="guardar_notas.php" id="<?= $asignatura_id ?>" data-asignatura="<?= $asignatura_id ?>">
                        <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
                        <input type="hidden" name="asignatura_id" value="<?= $asignatura_id ?>">
                        <h4 class="asignatura" style="position:relative; z-index: 999999;"><?= htmlspecialchars($asignatura['nombre']) ?></h4>

                        <table border="1" cellpadding="5" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>RUT</th>
                                    <?php for ($i = 1; $i <= 9; $i++): ?>
                                        <th>Nota <?= $i ?></th>
                                    <?php endfor; ?>
                                    <th>x̄</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conexion->prepare("
                                    SELECT 
                                        e.id AS estudiante_id, e.nombre, e.rut, 
                                        n.nota1, n.nota2, n.nota3, n.nota4, n.nota5,
                                        n.nota6, n.nota7, n.nota8, n.nota9, n.x̄
                                    FROM estudiantes e
                                    LEFT JOIN notas n ON n.estudiante_id = e.id AND n.asignatura_id = ?
                                    WHERE e.curso_id = ?
                                    ORDER BY e.nombre
                                ");
                                $stmt->bind_param("ii", $asignatura_id, $curso_id);
                                $stmt->execute();
                                $resultados = $stmt->get_result();

                                $contador = 1; // Aquí empieza el contador

                                while ($fila = $resultados->fetch_assoc()):
                                    $est_id = $fila['estudiante_id'];
                                ?>
                                    <tr data-lista="<?= $contador ?>">
                                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                        <td><?= htmlspecialchars($fila['rut']) ?></td>
                                        <?php for ($i = 1; $i <= 9; $i++): 
                                            $nota_valor = $fila["nota$i"];
                                        ?>
                                            <td>
                                                <input type="number" step="0.1" name="notas[<?= $est_id ?>][nota<?= $i ?>]"
                                                    value="<?= is_numeric($nota_valor) ? $nota_valor : '' ?>" min="1" max="7">
                                            </td>
                                        <?php endfor; ?>
                                        <td class="promedio"><?= htmlspecialchars($fila['x̄']) ?></td>
                                    </tr>
                                <?php 
                                    $contador++; // Incrementa para la siguiente fila
                                endwhile; 
                                $stmt->close(); 
                                ?>

                            </tbody>
                        </table>
                        <button type="submit">💾 Guardar Notas</button>
                    </form>
                <?php endwhile; ?>

            
                            
            
            </div>
            
        </div>

    </main>
    <aside class="nav-bottom">
        <nav>
            <ul>
                <li style="background: white;"><img class="icon" src="/assets/img/logo.svg" alt=""></li>
                <li><a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a></li>
                <li class="seleccionada"><a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a></li>
                <li><a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a></li> 
            </ul>
            <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
        </nav>
    </aside>

    
</body>
</html>

<script>

const coloresAsignaturas = {
    "Ciencias": "#0da761",
    "Matemáticas": "#3891e9",
    "Lenguaje": "#f75353",
    "Estudios Sociales": "#ed861f",
    "Inglés": "#cdb51a",
    "Inglés Comunicativo": "#23babf",
    "TICs": "#8544cf"
};

document.querySelectorAll('.asignatura').forEach(element => {
    const nombre = element.innerText.trim();

    for (const clave in coloresAsignaturas) {
        if (nombre.includes(clave)) {
            element.style.backgroundColor = coloresAsignaturas[clave];
            break;
        }
    }
});


let asignaturas = document.querySelectorAll('.curso');

asignaturas.forEach((element) => {
    const texto = element.textContent.trim();

    if (texto.includes("1° Nivel")) {
        element.style.backgroundColor = "#0da761"; // Verde
    } else if (texto.includes("2° Nivel")) {
        element.style.backgroundColor = "#3891e9"; // Azul
    }
});

document.querySelectorAll('.nombre-estudiante a').forEach(function(td) {
    const maxChars = 20;
    const texto = td.textContent.trim();

    if (texto.length > maxChars) {
        td.textContent = texto.substring(0, maxChars) + '...';
    }
});

document.querySelectorAll(".abrir-asignatura").forEach(boton => {
    boton.addEventListener("click", function() {
        const idAsignatura = this.getAttribute("data-asignatura");

        // Quitar clase activa de todos los botones
        document.querySelectorAll(".abrir-asignatura").forEach(btn => {
            btn.classList.remove("activa");
        });

        // Agregar clase activa al botón actual
        this.classList.add("activa");

        // Ocultar todos los formularios
        document.querySelectorAll(".notas-asignatura").forEach(form => {
            form.style.display = "none";
        });

        // Mostrar el formulario correspondiente
        const formulario = document.querySelector(`.notas-asignatura[data-asignatura="${idAsignatura}"]`);
        if (formulario) {
            formulario.style.display = "block";
        }
    });
});

window.addEventListener("DOMContentLoaded", () => {
    const primerBoton = document.querySelector(".abrir-asignatura");
    if (primerBoton) {
        primerBoton.click();
    }
});

let cerrarContenedor = document.querySelectorAll(".cerrar-contenedor");

cerrarContenedor.forEach(function(element) {
  element.addEventListener("click", function() {
    this.parentElement.classList.remove("active");
    document.body.classList.remove("no-scroll");
  });
});

function AbrirContenedor() {
    const idContenedor = this.getAttribute("data-modal");
    const modal = document.getElementById(idContenedor);
  
    if (modal) {
      modal.classList.add("active");
      document.body.classList.add("no-scroll");
    }
  }

document.querySelectorAll("[data-modal]").forEach(boton => {
    boton.addEventListener("click", AbrirContenedor);
});

document.getElementById("select-curso").addEventListener("change", function () {
    const cursoId = this.value;
    const selectEstudiante = document.getElementById("select-estudiante");

    if (!cursoId) {
        selectEstudiante.innerHTML = '<option value="">-- Selecciona primero un curso --</option>';
        selectEstudiante.disabled = true;
        return;
    }

    fetch(`obtener_estudiantes.php?curso_id=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            selectEstudiante.innerHTML = '<option value="">-- Seleccionar estudiante --</option>';
            data.forEach(est => {
                const option = document.createElement("option");
                option.value = est.id;
                option.textContent = est.nombre;
                selectEstudiante.appendChild(option);
            });
            selectEstudiante.disabled = false;
        })
        .catch(() => {
            selectEstudiante.innerHTML = '<option value="">Error al cargar</option>';
            selectEstudiante.disabled = true;
        });
});

document.getElementById("widget-estudiante").addEventListener("click", function () {
    const botones = document.querySelector(".contenedor-boton-estudiante")

    botones.classList.toggle("active")
})

const promedio = document.querySelectorAll(".promedio"); 

promedio.forEach(elemento => {
    const valor = parseFloat(elemento.textContent);

    if (valor < 4) {
        elemento.style.backgroundColor = "#e24a4a";
    } else if (valor >= 4) {
        elemento.style.backgroundColor = "#2589df"; 
    }
});

function generarMedidor(promedio) {
  const radio = 60;
  const circunferencia = 2 * Math.PI * radio;
  const porcentaje = Math.min(Math.max(promedio, 0), 7); // Asegurar que esté entre 0 y 7
  const offset = circunferencia * (1 - porcentaje / 7);

  // Elegir color según el promedio
  let color;
  if (promedio >= 5) {
    color = "#0da761";
  } else if (promedio >= 4) {
    color = "#f2a400"; // Amarillo
  } else {
    color = "#eb3b3b"; // Rojo
  }

  return `
    <svg width="150" height="190" viewBox="0 0 180 220" xmlns="http://www.w3.org/2000/svg" style="font-family: sans-serif;">
      <circle cx="90" cy="90" r="${radio}" stroke="#ddd" stroke-width="12" fill="none"/>
      <circle cx="90" cy="90" r="${radio}" stroke="${color}" stroke-width="12" fill="none"
        stroke-dasharray="${circunferencia}" 
        stroke-dashoffset="${offset}"
        stroke-linecap="round"
        transform="rotate(-90 90 90)" />
      <text x="90" y="100" text-anchor="middle" font-family="outfit" font-size="32" fill="white">${promedio.toFixed(1)}</text>
      <text x="90" y="190" text-anchor="middle" font-family= "outfit" font-size="16" fill="white" font-weight="bold" letter-spacing="2">
        ${promedio >= 6 ? "EXCELENTE" : promedio >= 5 ? "BIEN" : promedio >= 4 ? "REGULAR" : "NECESITA MEJORAR"}
      </text>
      <text x="90" y="210" text-anchor="middle" font-size="12" fill="#c9c9c9" letter-spacing="2">PROMEDIO GENERAL</text>
    </svg>`;
}

function calcularYMostrarPromedio() {
  const elementos = document.querySelectorAll('.promedio');
  const valores = Array.from(elementos).map(e => parseFloat(e.textContent)).filter(n => !isNaN(n));
  if (valores.length === 0) return;

  const promedio = valores.reduce((a, b) => a + b, 0) / valores.length;

  const grafico = generarMedidor(promedio);
  document.getElementById('grafico-promedio').innerHTML = grafico;
}

calcularYMostrarPromedio();

const conteoPorLista = {};

// Recorremos todos los <tr> con data-lista
document.querySelectorAll('tr[data-lista]').forEach(tr => {
  const lista = tr.getAttribute('data-lista');
  const promedio = tr.querySelector('.promedio');
  const valor = promedio?.textContent.trim();

  if (!valor || isNaN(parseFloat(valor))) {
    if (!conteoPorLista[lista]) {
      conteoPorLista[lista] = 0;
    }
    conteoPorLista[lista]++;
  } else {
    // Aseguramos que cada data-lista tenga valor aunque no tenga promedio vacío
    if (!conteoPorLista[lista]) {
      conteoPorLista[lista] = 0;
    }
  }
});

// Obtener el valor máximo de promedios vacíos
const valores = Object.values(conteoPorLista);
const maxVacios = Math.max(...valores);

// Contar cuántos data-lista tienen exactamente ese valor máximo
const cantidadMaximos = valores.filter(v => v === maxVacios).length;

document.getElementById("estudiantes-sin-notas").innerHTML = `Hay ${cantidadMaximos} estudiantes sin notas en todas las asiganturas.`;

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


