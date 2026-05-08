<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Método no permitido.");
}

function limpiar($campo) {
    return htmlspecialchars(trim($campo));
}

// Datos del estudiante
$nombre_estudiante               = limpiar($_POST['nombre_estudiante']);
$apellidos_estudiante            = limpiar($_POST['apellidos_estudiante']);
$fecha_nacimiento                = limpiar($_POST['fecha_nacimiento']);
$rut_estudiante                  = limpiar($_POST['rut_estudiante']);
$serie_carnet_estudiante         = limpiar($_POST['serie_carnet_estudiante']);
$etnia_estudiante                = limpiar($_POST['etnia_estudiante']);
$direccion_estudiante            = limpiar($_POST['direccion_estudiante']);
$correo_estudiante               = limpiar($_POST['correo_estudiante']);
$jornada_preferida               = '';
$telefono_estudiante             = limpiar($_POST['telefono_estudiante']);
$hijos_estudiante                = limpiar($_POST['hijos_estudiante']);
$situacion_especial_estudiante   = limpiar($_POST['situacion_especial_estudiante']);
$programa_estudiante             = limpiar($_POST['programa_estudiante']);
$curso_preferido                 = (int)limpiar($_POST['curso_preferido']);

// Datos del apoderado
$nombre_apoderado                = limpiar($_POST['nombre_apoderado']);
$rut_apoderado                   = limpiar($_POST['rut_apoderado']);
$parentezco_apoderado            = limpiar($_POST['parentezco_apoderado']);
$direccion_apoderado             = limpiar($_POST['direccion_apoderado']);
$telefono_apoderado              = limpiar($_POST['telefono_apoderado']);
$situacion_especial_apoderado    = limpiar($_POST['situacion_especial_apoderado']);

function asignarJornadaPorCurso($curso) {
    $jornadas = [
        'Mañana' => [1, 4, 5],
        'Tarde'  => [2, 6, 7],
        'Noche'  => [3, 8, 9]
    ];
    foreach ($jornadas as $jornada => $cursos) {
        if (in_array($curso, $cursos)) {
            return $jornada;
        }
    }
    return 'Sin información';
}

if (isset($_POST['jornada_preferida']) && !empty($_POST['jornada_preferida'])) {
    $jornada_preferida = limpiar($_POST['jornada_preferida']);
} else {
    $jornada_preferida = asignarJornadaPorCurso($curso_preferido);
}

// ─────────────────────────────────────────
// INICIAR TRANSACCIÓN
// ─────────────────────────────────────────
$conexion->begin_transaction();

try {

    // 1️⃣ INSERTAR MATRÍCULA
    $sql = "INSERT INTO matriculas (
                nombre_estudiante,
                apellidos_estudiante,
                fecha_nacimiento,
                rut_estudiante,
                serie_carnet_estudiante,
                etnia_estudiante,
                direccion_estudiante,
                correo_estudiante,
                jornada_preferida,
                telefono_estudiante,
                hijos_estudiante,
                situacion_especial_estudiante,
                programa_estudiante,
                curso_preferido,
                nombre_apoderado,
                rut_apoderado,
                parentezco_apoderado,
                direccion_apoderado,
                telefono_apoderado,
                situacion_especial_apoderado,
                fecha_registro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error preparando matrícula: " . $conexion->error);
    }

    $hijos_estudiante_int = (int)$hijos_estudiante;

    $stmt->bind_param(
        "ssssssssssisssssssss",
        $nombre_estudiante,
        $apellidos_estudiante,
        $fecha_nacimiento,
        $rut_estudiante,
        $serie_carnet_estudiante,
        $etnia_estudiante,
        $direccion_estudiante,
        $correo_estudiante,
        $jornada_preferida,
        $telefono_estudiante,
        $hijos_estudiante_int,
        $situacion_especial_estudiante,
        $programa_estudiante,
        $curso_preferido,
        $nombre_apoderado,
        $rut_apoderado,
        $parentezco_apoderado,
        $direccion_apoderado,
        $telefono_apoderado,
        $situacion_especial_apoderado
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar matrícula: " . $stmt->error);
    }

    $matricula_id = $conexion->insert_id;
    $stmt->close();

    // 2️⃣ INSERTAR ESTUDIANTE
    $sqlEst = "INSERT INTO estudiantes (matricula_id, curso_id) VALUES (?, ?)";
    $stmtEst = $conexion->prepare($sqlEst);

    if (!$stmtEst) {
        throw new Exception("Error preparando estudiante: " . $conexion->error);
    }

    $stmtEst->bind_param("ii", $matricula_id, $curso_preferido);

    if (!$stmtEst->execute()) {
        throw new Exception("Error al insertar estudiante: " . $stmtEst->error);
    }

    $estudiante_id = $conexion->insert_id;
    $stmtEst->close();

    // 3️⃣ ACTUALIZAR estudiante_id Y ESTADO EN MATRÍCULA
    $sqlUpd = "UPDATE matriculas SET estudiante_id = ?, estado = 'Activa' WHERE id = ?";
    $stmtUpd = $conexion->prepare($sqlUpd);

    if (!$stmtUpd) {
        throw new Exception("Error preparando update: " . $conexion->error);
    }

    $stmtUpd->bind_param("ii", $estudiante_id, $matricula_id);

    if (!$stmtUpd->execute()) {
        throw new Exception("Error al actualizar matrícula: " . $stmtUpd->error);
    }

    $stmtUpd->close();

    // ✅ Todo bien, confirmar transacción
    $conexion->commit();

    header("Location: matriculas.php?mensaje=activada");
    exit();

} catch (Exception $e) {
    // ❌ Algo falló, revertir todo
    $conexion->rollback();
    echo "Error en el proceso: " . $e->getMessage();
}

$conexion->close();
?>