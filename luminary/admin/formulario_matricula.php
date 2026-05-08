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

    form {
  display: flex;
  flex-direction: column;
  min-height: 0;
  gap: 1rem;
  flex: 1;
  position: relative;
  border-radius: 1rem;
}

form.white {
  background-color: white;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.15);
  padding: 2rem;
  flex: none;
}


form.white .campo {
  gap: .5rem;
}

form.white .campo input {
  border: none;
  border-bottom : 1px solid rgba(0, 0, 0, 0.5);
  padding: 0.5rem;
  margin-bottom: .5rem;
  background-color: transparent;
}

form.white .campo input:focus {
  outline: none;
  border-bottom: 2px solid #126abd;
  background-color: transparent;
}

form.white h3:first-child {
  margin-top: 0;
}

.campo {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
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
        <form class="white" action="matriculas_crear.php" method="POST">
            <h3>Datos del Estudiante</h3>
            <div class="campo">
                <label>Nombre:</label>
                <input type="text" class="mayusculas" name="nombre_estudiante" required>
            </div>
            
            <div class="campo">
                <label>Apellidos:</label>
                <input type="text" class="mayusculas" name="apellidos_estudiante" required>
            </div>

            <div class="campo">
                <label>Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required>
            </div>

            <div class="campo">
                <label>RUT:</label>
                <input type="text" name="rut_estudiante" class="formateador_rut_simple" required>
            </div>

            <div class="campo">
                <label>N° Serie Carnet:</label>
                <input type="text" name="serie_carnet_estudiante">
            </div>

            <div class="campo">
                <label>Etnia Estudiante:</label>
                <select class="select-1" name="etnia_estudiante" id="etnia_estudiante">
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
                </select>
            </div>

            <div class="campo">
                <label>Dirección Estudiante:</label>
                <input type="text" class="mayusculas" name="direccion_estudiante">
            </div>

            <div class="campo">
                <label>Correo electrónico:</label>
                <input type="text" name="correo_estudiante">
            </div>

            <div class="campo">
                <label>Curso y modalidad:</label>
                <select class="select-1" name="curso_preferido" id="curso_preferido" required>
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
                </select>
            </div>

            <div class="campo">
                <label>Teléfono:</label>
                <input type="text" name="telefono_estudiante">
            </div>

            <div class="campo">
                <label>Hijos Estudiante:</label>
                <input type="number" name="hijos_estudiante">
            </div>

            <div class="campo">
                <label>Situación Especial:</label>
                <select class="select-1" name="situacion_especial_estudiante" id="situacion_especial_estudiante">
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
            </select>

            <div class="campo">
                <label>Programa Especial:</label>
                <input type="text" class="mayusculas" name="programa_estudiante">
            </div>

            <h3>Datos del Apoderado</h3>

            <div class="campo">
                <label>Nombre Completo:</label>
                <input type="text" class="mayusculas" name="nombre_apoderado">
            </div>

            <div class="campo">
                <label>RUT Apoderado:</label>
                <input type="text" name="rut_apoderado" class="formateador_rut_simple">
            </div>

            <div class="campo">
                <label>Parentezco:</label>
                <select class="select-1" type="text" name="parentezco_apoderado">
                    <option value="">Seleccione una opción</option>
                    <option value="Madre">Madre</option>
                    <option value="Padre">Padre</option>
                    <option value="Hermano/a">Hermano/a</option>
                <option value="Tutor">Tutor</option>
                <option value="Otro">Otro</option>
            </select>

            <div class="campo">
                <label>Dirección Apoderado:</label>
                <input type="text" class="mayusculas" name="direccion_apoderado">
            </div>

            <div class="campo">
                <label>Teléfono Apoderado:</label>
                <input type="text" name="telefono_apoderado">
            </div>

            <div class="campo">
                <label>Otros:</label>
                <input type="text" class="mayusculas" name="situacion_especial_apoderado">
            </div>

            <button type="submit">Crear matrícla</button>
        </form>
    </main>
    <?php
        include "components/aside_bottom.php"
    ?>
</body>
<script>
    const inputRut = document.querySelectorAll('.formateador_rut_simple');

    inputRut.forEach(function(rutInput) {
        rutInput.addEventListener('input', function() {
            // Obtener posición del cursor antes de modificar
            let cursor = this.selectionStart;

        // Eliminar caracteres no válidos: solo permitir números y K
        let valor = this.value.toUpperCase().replace(/[^0-9K]/g, '');

        // Agregar guion antes del dígito verificador si hay más de 1 carácter
        if (valor.length > 1) {
            valor = valor.slice(0, -1) + '-' + valor.slice(-1);
        }

        this.value = valor;
    })});

    inputsMayusculas = document.querySelectorAll(".mayusculas");

    inputsMayusculas.forEach((input) => {
        input.addEventListener("input", () => {
            input.value = input.value.toUpperCase();
        });
    });
</script>
</html>

