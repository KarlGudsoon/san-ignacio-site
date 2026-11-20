<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

const ANIO_PROXIMO = 2027;

$id_matricula = (int)($_POST["id_matricula"] ?? 0);
$tabla_origen = $_POST["tabla_origen"] ?? "matriculas_formulario";

if (!$id_matricula) {
    echo json_encode(["success" => false, "message" => "Falta id_matricula"]);
    exit;
}

if ($tabla_origen !== "matriculas_formulario") {
    echo json_encode(["success" => false, "message" => "Tabla de origen no válida"]);
    exit;
}

$columnas = [
    "matricula_ano",
    "nombre_estudiante",
    "apellidos_estudiante",
    "fecha_nacimiento",
    "rut_estudiante",
    "serie_carnet_estudiante",
    "etnia_estudiante",
    "direccion_estudiante",
    "correo_estudiante",
    "curso_preferido",
    "jornada_preferida",
    "telefono_estudiante",
    "hijos_estudiante",
    "situacion_especial_estudiante",
    "programa_estudiante",
    "nombre_apoderado",
    "rut_apoderado",
    "parentezco_apoderado",
    "direccion_apoderado",
    "telefono_apoderado",
    "situacion_especial_apoderado",
    "fecha_registro",
    "estado",
];

$conexion->begin_transaction();

try {
    $sqlOrigen = "SELECT " . implode(", ", $columnas) . " FROM $tabla_origen WHERE id = ? LIMIT 1";
    $stmtOrigen = $conexion->prepare($sqlOrigen);

    if (!$stmtOrigen) {
        throw new Exception("Error al leer la matrícula de origen");
    }

    $stmtOrigen->bind_param("i", $id_matricula);
    $stmtOrigen->execute();
    $origen = $stmtOrigen->get_result()->fetch_assoc();
    $stmtOrigen->close();

    if (!$origen) {
        throw new Exception("Matrícula no encontrada");
    }

    $estado_origen = trim((string)($origen["estado"] ?? ""));

    if (strcasecmp($estado_origen, "Copiada") === 0) {
        throw new Exception("Esta matrícula ya fue copiada");
    }

    $rut_estudiante = trim((string)($origen["rut_estudiante"] ?? ""));

    if ($rut_estudiante === "") {
        throw new Exception("La matrícula no tiene RUT de estudiante");
    }

    $stmtRut = $conexion->prepare("SELECT id FROM matriculas_proximo_anio WHERE rut_estudiante = ? LIMIT 1");

    if (!$stmtRut) {
        throw new Exception("Error al validar el RUT en próximo año");
    }

    $stmtRut->bind_param("s", $rut_estudiante);
    $stmtRut->execute();
    $rut_duplicado = $stmtRut->get_result()->num_rows > 0;
    $stmtRut->close();

    if ($rut_duplicado) {
        throw new Exception("Ya existe una matrícula con ese RUT en próximo año");
    }

    $matricula_ano = ANIO_PROXIMO;
    $nombre_estudiante = (string)($origen["nombre_estudiante"] ?? "");
    $apellidos_estudiante = (string)($origen["apellidos_estudiante"] ?? "");
    $fecha_nacimiento = (string)($origen["fecha_nacimiento"] ?? "");
    $rut_estudiante = (string)($origen["rut_estudiante"] ?? "");
    $serie_carnet_estudiante = (string)($origen["serie_carnet_estudiante"] ?? "");
    $etnia_estudiante = (string)($origen["etnia_estudiante"] ?? "");
    $direccion_estudiante = (string)($origen["direccion_estudiante"] ?? "");
    $correo_estudiante = (string)($origen["correo_estudiante"] ?? "");
    $curso_preferido = (int)($origen["curso_preferido"] ?? 0);
    $jornada_preferida = (string)($origen["jornada_preferida"] ?? "");
    $telefono_estudiante = (string)($origen["telefono_estudiante"] ?? "");
    $hijos_estudiante = (int)($origen["hijos_estudiante"] ?? 0);
    $situacion_especial_estudiante = (string)($origen["situacion_especial_estudiante"] ?? "");
    $programa_estudiante = (string)($origen["programa_estudiante"] ?? "");
    $nombre_apoderado = (string)($origen["nombre_apoderado"] ?? "");
    $rut_apoderado = (string)($origen["rut_apoderado"] ?? "");
    $parentezco_apoderado = (string)($origen["parentezco_apoderado"] ?? "");
    $direccion_apoderado = (string)($origen["direccion_apoderado"] ?? "");
    $telefono_apoderado = (string)($origen["telefono_apoderado"] ?? "");
    $situacion_especial_apoderado = (string)($origen["situacion_especial_apoderado"] ?? "");
    $fecha_registro = (string)($origen["fecha_registro"] ?? date("Y-m-d H:i:s"));
    $estado = $estado_origen !== "" ? $estado_origen : "Pendiente";

    $sqlInsert = "INSERT INTO matriculas_proximo_anio (
            matricula_ano,
            nombre_estudiante,
            apellidos_estudiante,
            fecha_nacimiento,
            rut_estudiante,
            serie_carnet_estudiante,
            etnia_estudiante,
            direccion_estudiante,
            correo_estudiante,
            curso_preferido,
            jornada_preferida,
            telefono_estudiante,
            hijos_estudiante,
            situacion_especial_estudiante,
            programa_estudiante,
            nombre_apoderado,
            rut_apoderado,
            parentezco_apoderado,
            direccion_apoderado,
            telefono_apoderado,
            situacion_especial_apoderado,
            fecha_registro,
            estado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtInsert = $conexion->prepare($sqlInsert);

    if (!$stmtInsert) {
        throw new Exception("Error al preparar la copia");
    }

    $stmtInsert->bind_param(
        "issssssssississssssssss",
        $matricula_ano,
        $nombre_estudiante,
        $apellidos_estudiante,
        $fecha_nacimiento,
        $rut_estudiante,
        $serie_carnet_estudiante,
        $etnia_estudiante,
        $direccion_estudiante,
        $correo_estudiante,
        $curso_preferido,
        $jornada_preferida,
        $telefono_estudiante,
        $hijos_estudiante,
        $situacion_especial_estudiante,
        $programa_estudiante,
        $nombre_apoderado,
        $rut_apoderado,
        $parentezco_apoderado,
        $direccion_apoderado,
        $telefono_apoderado,
        $situacion_especial_apoderado,
        $fecha_registro,
        $estado
    );

    if (!$stmtInsert->execute()) {
        throw new Exception("Error al insertar la copia");
    }

    $id_nuevo = $conexion->insert_id;
    $stmtInsert->close();

    $stmtUpdate = $conexion->prepare("UPDATE matriculas_formulario SET estado = 'Copiada' WHERE id = ?");

    if (!$stmtUpdate) {
        throw new Exception("Error al marcar la matrícula como copiada");
    }

    $stmtUpdate->bind_param("i", $id_matricula);

    if (!$stmtUpdate->execute()) {
        throw new Exception("Error al marcar la matrícula como copiada");
    }

    $stmtUpdate->close();
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Matrícula copiada a próximo año correctamente",
        "id_nuevo" => $id_nuevo,
    ]);
} catch (Throwable $e) {
    $conexion->rollback();

    error_log("Error al copiar matrícula: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
    ]);
}
