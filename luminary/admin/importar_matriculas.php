<?php
session_start();
require_once "../conexion.php"; 
header("Content-Type: text/plain; charset=utf-8");

// Recibir JSON enviado desde JavaScript
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data) {
    exit("No se recibieron datos válidos.");
}

// 1️⃣ Insertar matrícula
$sql_matricula = "INSERT INTO matriculas 
        (nombre_estudiante, apellidos_estudiante, fecha_nacimiento, rut_estudiante, direccion_estudiante, correo_estudiante, telefono_estudiante, curso_preferido, estado, estudiante_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Activa', NULL)";

$stmt_m = $conexion->prepare($sql_matricula);

if (!$stmt_m) {
    exit("Error en prepare(): " . $conexion->error);
}

// 2️⃣ Insertar estudiante
$sql_estudiante = "INSERT INTO estudiantes (matricula_id, curso_id)
                   VALUES (?, ?)";

$stmt_e = $conexion->prepare($sql_estudiante);

if (!$stmt_e) {
    exit("Error en prepare estudiantes(): " . $conexion->error);
}

// 3️⃣ Actualizar matrícula con el id del estudiante
$sql_update = "UPDATE matriculas SET estudiante_id = ? WHERE id = ?";
$stmt_u = $conexion->prepare($sql_update);

if (!$stmt_u) {
    exit("Error en prepare update(): " . $conexion->error);
}

$contador = 0;

foreach ($data as $fila) {

    // Datos desde el JSON
    $nombres = $fila["nombres"] ?? "";
    $ap_pat = $fila["apellido_paterno"] ?? "";
    $ap_mat = $fila["apellido_materno"] ?? "";
    $f_naci = $fila["fecha_nacimiento"] ?? "";
    $rut_num = $fila["rut"] ?? "";
    $dv = $fila["codigo_verificador"] ?? "";
    $direccion = $fila["direccion"] ?? "";
    $correo = $fila["email"] ?? "";
    $telefono = $fila["telefono"] ?? "";
    $codigo_curso = $fila["codigo_curso"] ?? "";
    $letra_curso  = strtoupper($fila["letra_curso"] ?? "");
    
    // ---------------------------
    // REGLAS DE CURSOS
    // ---------------------------
    $curso_preferido = null;
    if ($codigo_curso == "1" && $letra_curso == "A") $curso_preferido = 1;
    if ($codigo_curso == "1" && $letra_curso == "B") $curso_preferido = 2;
    if ($codigo_curso == "1" && $letra_curso == "C") $curso_preferido = 3;

    if ($codigo_curso == "3" && $letra_curso == "A") $curso_preferido = 4;
    if ($codigo_curso == "3" && $letra_curso == "B") $curso_preferido = 5;
    if ($codigo_curso == "3" && $letra_curso == "C") $curso_preferido = 6;
    if ($codigo_curso == "3" && $letra_curso == "D") $curso_preferido = 7;
    if ($codigo_curso == "3" && $letra_curso == "E") $curso_preferido = 8;
    if ($codigo_curso == "3" && $letra_curso == "F") $curso_preferido = 9;

    // Apellidos juntos
    $apellidos = trim($ap_pat . " " . $ap_mat);

    // Rut final
    $rut_final = $rut_num . "-" . $dv;

    // -----------------------------------
    // 1️⃣ Insertar MATRÍCULA
    // -----------------------------------
    $stmt_m->bind_param("sssssssi",
        $nombres,
        $apellidos,
        $f_naci,
        $rut_final,
        $direccion,
        $correo,
        $telefono,
        $curso_preferido
    );

    if (!$stmt_m->execute()) {
        echo "Error insertando matrícula: " . $stmt_m->error;
        continue;
    }

    // ID de la matrícula recién creada
    $matricula_id = $conexion->insert_id;

    // -----------------------------------
    // 2️⃣ Crear ESTUDIANTE vinculado
    // -----------------------------------
    $stmt_e->bind_param("ii",
        $matricula_id,
        $curso_preferido
    );

    if (!$stmt_e->execute()) {
        echo "Error insertando estudiante: " . $stmt_e->error;
        continue;
    }

    // ID del estudiante recién creado
    $estudiante_id = $conexion->insert_id;

    // -----------------------------------
    // 3️⃣ Actualizar matrícula con estudiante_id
    // -----------------------------------
    $stmt_u->bind_param("ii",
        $estudiante_id,
        $matricula_id
    );

    $stmt_u->execute();

    $contador++;
}

// Respuesta final
echo "Importación completada. Registros insertados: $contador";
?>
