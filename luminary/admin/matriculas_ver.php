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
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: .9rem;
        }
    </style>
</head>

<body>

<?php
include "components/aside.php"
?>

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
                <div><?= htmlspecialchars($d['rut_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Fecha de Nacimiento</label>
                <div><?= date("d/m/Y", strtotime($d['fecha_nacimiento'] ?: "Sin información")) ?></div>
            </div>

            <div class="campo">
                <label>N° Serie Carnet</label>
                <div><?= htmlspecialchars($d['serie_carnet_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Etnia Estudiante</label>
                <div><?= htmlspecialchars($d['etnia_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Dirección Estudiante</label>
                <div><?= htmlspecialchars($d['direccion_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Correo Electrónico Estudiante</label>
                <div><?= htmlspecialchars($d['correo_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label><?= ($d['estado'] === 'Activa') ? 'Curso Actual' : 'Curso Preferido' ?></label>
                <div>
                    <?= $d['nivel'] ? $d['nivel'] . " Nivel " . $d['letra'] : "No asignado" ?>
                </div>
            </div>

            <div class="campo">
                <label>Jornada Preferida</label>
                <div><?= htmlspecialchars($d['jornada_preferida'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Telefono Estudiante</label>
                <div><?= htmlspecialchars($d['telefono_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Hijos del Estudiante</label>
                <div><?= htmlspecialchars($d['hijos_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Situación Especial Estudiante</label>
                <div><?= htmlspecialchars($d['situacion_especial_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Programa Especial</label>
                <div><?= htmlspecialchars($d['programa_estudiante'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Nombre Apoderado</label>
                <div><?= htmlspecialchars($d['nombre_apoderado'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>RUT Apoderado</label>
                <div><?= htmlspecialchars($d['rut_apoderado'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Parentezco</label>
                <div><?= htmlspecialchars($d['parentezco_apoderado'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Dirección Apoderado</label>
                <div><?= htmlspecialchars($d['direccion_apoderado'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Teléfono Apoderado</label>
                <div><?= htmlspecialchars($d['telefono_apoderado'] ?: "Sin información") ?></div>
            </div>

            <div class="campo">
                <label>Situación Especial Apoderado</label>
                <div><?= htmlspecialchars($d['hijos_estudiante'] ?: "Sin información") ?></div>
            </div>

            

            <div class="campo">
                <label>Fecha Registro</label>
                <div><?= date("d/m/Y", strtotime($d['fecha_registro'])) ?></div>
            </div>

        </div>

        <div style="display: flex;">
            <a href="matriculas.php" class="btn-volver">⬅ Volver</a>
            <a href="generar_ficha_matricula.php?id=<?= $d['id'] ?>" target="_blank" class="btn-volver" style="background: red; margin-left: 1rem;"> <img src="/assets/icon/streamline--convert-pdf-2-solid.svg"> Ficha PDF</a>
        </div>

        
    </div>

</main>
<?php
include "components/aside_bottom.php"
?>

</body>
</html>
