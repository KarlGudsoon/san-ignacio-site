<?php 
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear ficha de matrícula de estudiante</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="../public/global.css">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    select {
        width: fit-content;
    }
</style>
<body>
    <?php
        include "components/aside.php"
    ?>
    <main>
        <a href="javascript:history.back()" class="volver"><img src="/assets/icons/arrow.svg"></a>
        <div class="contenedor-informacion">
            <h1>Crear ficha de matrícula</h1>
            <p>Aquí podrás crear una ficha de matrícula una vez completado el siguiente formulario.</p>
        </div>
        <h2>Datos del Estudiante</h2>
        <form action="matriculas_crear.php" method="POST">

            <label>Nombre:</label>
            <input type="text" name="nombre_estudiante" required><br><br>

            <label>Apellidos:</label>
            <input type="text" name="apellidos_estudiante" required><br><br>

            <label>Fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" required><br><br>

            <label>RUT:</label>
            <input type="text" name="rut_estudiante" id="formateador_rut" required><br><br>

            <label>N° Serie Carnet:</label>
            <input type="text" name="serie_carnet_estudiante"><br><br>

            <label>Etnia Estudiante:</label>
            <select name="etnia_estudiante" id="etnia_estudiante">
                    <option value="Ninguna">Ninguno</option>
                    <option value="Mapuche">Mapuche</option>
                    <option value="Aymara">Aymara</option>
                    <option value="Rapa Nui">Rapa Nui (Pascuense)</option>
                    <option value="Quechua">Quechua</option>
                    <option value="Atacameño">Atacameño (Lickan Antay)</option>
                    <option value="Colla">Colla</option>
                    <option value="Diaguita">Diaguita</option>
                    <option value="Kawésqar">Kawésqar (Alacalufe)</option>
                    <option value="Yagán">Yagán (Yámana)</option>
                    <option value="Chango">Chango</option>
                    <option value="Afrodescendiente">Afrodescendiente chileno</option>
                    <option value="Otro">Otro pueblo originario</option>
                    <option value="Prefiero no responder">Prefiero no responder</option>
                </select><br><br>

            <label>Dirección Estudiante:</label>
            <input type="text" name="direccion_estudiante"><br><br>

            <label>Correo electrónico:</label>
            <input type="text" name="correo_estudiante"><br><br>

            <label>Curso y modalidad:</label>
            <select name="curso_preferido" id="curso_preferido" required>
                <option disabled selected>Seleccione una opción</option>
                <optgroup label="Jornada Mañana">
                    <option value="1">1° Nivel A (Mañana)</option>
                    <option value="4">2° Nivel A (Mañana)</option>
                    <option value="5">2° Nivel B (Mañana)</option>
                </optgroup>
                <optgroup label="Jornada Tarde">
                    <option value="2">1° Nivel B (Tarde)</option>
                    <option value="6">2° Nivel C (Tarde)</option>
                    <option value="7">2° Nivel D (Tarde)</option>
                </optgroup>
                <optgroup label="Jornada Noche">
                    <option value="3">1° Nivel C (Noche)</option>
                    <option value="8">2° Nivel E (Noche)</option>
                    <option value="9">2° Nivel F (Noche)</option>
                </optgroup>
            </select><br><br>

            <label>Teléfono:</label>
            <input type="text" name="telefono_estudiante"><br><br>

            <label>Hijos Estudiante:</label>
            <input type="number" name="hijos_estudiante"><br><br>

            <label>Situación Especial:</label>
            <select name="situacion_especial_estudiante" id="situacion_especial_estudiante">
                <option selected value="Ninguna">Ninguna</option>
                <option value="Programa Social">Programa Social</option>
                <option value="Enfermedad Crónica/Grave">Enfermedad Crónica/Grave</option>
                <option value="Discapacidad Física/Movilidad Reducida">Discapacidad Física/Movilidad Reducida</option>
                <option value="Discapacidad Sensorial (Visual/Auditiva)">Discapacidad Sensorial (Visual/Auditiva)</option>
                <option value="Condición de Salud Mental">Condición de Salud Mental</option>
                <option value="Trastorno Específico del Aprendizaje">Trastorno Específico del Aprendizaje (Dislexia, etc.)</option>
                <option value="Necesidades Educativas Especiales">Necesidades Educativas Especiales (General)</option>
                <option value="Maternidad/Paternidad o Carga Familiar">Maternidad/Paternidad o Carga Familiar</option>
                <option value="Deportista de Alto Rendimiento">Deportista de Alto Rendimiento</option>
                <option value="Vulnerabilidad/Violencia">Vulnerabilidad/Violencia</option>
            </select><br><br>

            <label>Programa Especial:</label>
            <input type="text" name="programa_estudiante"><br><br>

            <h2>Datos del Apoderado</h2>

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_apoderado"><br><br>

            <label>RUT Apoderado:</label>
            <input type="text" name="rut_apoderado"><br><br>

            <label>Parentezco:</label>
            <select type="text" name="parentezco_apoderado">
                <option value="">Seleccione una opción</option>
                <option value="Madre">Madre</option>
                <option value="Padre">Padre</option>
                <option value="Hermano/a">Hermano/a</option>
                <option value="Tutor">Tutor</option>
                <option value="Otro">Otro</option>
            </select><br><br>

            <label>Dirección Apoderado:</label>
            <input type="text" name="direccion_apoderado"><br><br>

            <label>Teléfono Apoderado:</label>
            <input type="text" name="telefono_apoderado"><br><br>

            <label>Otros:</label>
            <input type="text" name="situacion_especial_apoderado"><br><br>

            <button type="submit">Crear matrícla</button>
        </form>
    </main>
    <?php
        include "components/aside_bottom.php"
    ?>
</body>
<script src="/public/js/script.js"></script>
</html>

