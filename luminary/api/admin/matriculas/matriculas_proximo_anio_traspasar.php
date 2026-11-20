<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

const ANIO_PROXIMO = 2027;

$tabla_destino = $_POST["tabla_destino"] ?? "matriculas_proximo_anio";

if ($tabla_destino !== "matriculas_proximo_anio") {
    echo json_encode(["success" => false, "message" => "Tabla destino no válida"]);
    exit;
}

$mapaCursos = [
    "1°|A" => ["nivel" => "2°", "letra" => "B"],
    "1°|B" => ["nivel" => "2°", "letra" => "D"],
    "1°|C" => ["nivel" => "2°", "letra" => "F"],
];

$conexion->begin_transaction();

try {
    $resultCursos = $conexion->query("SELECT id, nivel, letra, jornada FROM cursos");

    if (!$resultCursos) {
        throw new Exception("No se pudieron obtener los cursos");
    }

    $cursosPorClave = [];

    while ($curso = $resultCursos->fetch_assoc()) {
        $cursosPorClave[$curso["nivel"] . "|" . $curso["letra"]] = $curso;
    }

    $sqlOrigen = "SELECT e.id AS id_estudiante, e.matricula_id, e.curso_id, m.*,
                         c.nivel AS nivel_origen, c.letra AS letra_origen
                  FROM estudiantes e
                  INNER JOIN matriculas m ON m.id = e.matricula_id
                  INNER JOIN cursos c ON c.id = e.curso_id
                  WHERE c.nivel = '1°' AND m.estado = 'Activa'
                  ORDER BY c.letra, m.apellidos_estudiante";

    $resultOrigen = $conexion->query($sqlOrigen);

    if (!$resultOrigen) {
        throw new Exception("No se pudieron obtener los estudiantes de 1° nivel");
    }

    $stmtRut = $conexion->prepare("SELECT id FROM $tabla_destino WHERE rut_estudiante = ? LIMIT 1");

    if (!$stmtRut) {
        throw new Exception("Error al preparar la validación de RUT");
    }

    $sqlInsert = "INSERT INTO $tabla_destino (
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
        throw new Exception("Error al preparar la copia de matrículas");
    }

    $matricula_ano = ANIO_PROXIMO;
    $nombre_estudiante = "";
    $apellidos_estudiante = "";
    $fecha_nacimiento = "";
    $rut_estudiante = "";
    $serie_carnet_estudiante = "";
    $etnia_estudiante = "";
    $direccion_estudiante = "";
    $correo_estudiante = "";
    $curso_preferido = 0;
    $jornada_preferida = "";
    $telefono_estudiante = "";
    $hijos_estudiante = 0;
    $situacion_especial_estudiante = "";
    $programa_estudiante = "";
    $nombre_apoderado = "";
    $rut_apoderado = "";
    $parentezco_apoderado = "";
    $direccion_apoderado = "";
    $telefono_apoderado = "";
    $situacion_especial_apoderado = "";
    $fecha_registro = date("Y-m-d H:i:s");
    $estado = "Pendiente";

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

    $copiados = 0;
    $omitidos_duplicados = 0;
    $sin_mapeo = 0;
    $errores = 0;

    while ($fila = $resultOrigen->fetch_assoc()) {
        $claveOrigen = ($fila["nivel_origen"] ?? "") . "|" . ($fila["letra_origen"] ?? "");

        if (!isset($mapaCursos[$claveOrigen])) {
            $sin_mapeo++;
            continue;
        }

        $destino = $mapaCursos[$claveOrigen];
        $claveDestino = $destino["nivel"] . "|" . $destino["letra"];

        if (!isset($cursosPorClave[$claveDestino])) {
            $errores++;
            error_log("No existe el curso destino $claveDestino para $claveOrigen");
            continue;
        }

        $cursoDestino = $cursosPorClave[$claveDestino];

        $rut_estudiante = trim((string)($fila["rut_estudiante"] ?? ""));

        if ($rut_estudiante === "") {
            $errores++;
            continue;
        }

        $stmtRut->bind_param("s", $rut_estudiante);
        $stmtRut->execute();
        $resultRut = $stmtRut->get_result();
        $duplicado = $resultRut && $resultRut->num_rows > 0;

        if ($resultRut) {
            $resultRut->free();
        }

        if ($duplicado) {
            $omitidos_duplicados++;
            continue;
        }

        $matricula_ano = ANIO_PROXIMO;
        $nombre_estudiante = (string)($fila["nombre_estudiante"] ?? "");
        $apellidos_estudiante = (string)($fila["apellidos_estudiante"] ?? "");
        $fecha_nacimiento = (string)($fila["fecha_nacimiento"] ?? "");
        $serie_carnet_estudiante = (string)($fila["serie_carnet_estudiante"] ?? "");
        $etnia_estudiante = (string)($fila["etnia_estudiante"] ?? "");
        $direccion_estudiante = (string)($fila["direccion_estudiante"] ?? "");
        $correo_estudiante = (string)($fila["correo_estudiante"] ?? "");
        $curso_preferido = (int)($cursoDestino["id"] ?? 0);
        $jornada_preferida = (string)($cursoDestino["jornada"] ?? "");
        $telefono_estudiante = (string)($fila["telefono_estudiante"] ?? "");
        $hijos_estudiante = (int)($fila["hijos_estudiante"] ?? 0);
        $situacion_especial_estudiante = (string)($fila["situacion_especial_estudiante"] ?? "");
        $programa_estudiante = (string)($fila["programa_estudiante"] ?? "");
        $nombre_apoderado = (string)($fila["nombre_apoderado"] ?? "");
        $rut_apoderado = (string)($fila["rut_apoderado"] ?? "");
        $parentezco_apoderado = (string)($fila["parentezco_apoderado"] ?? "");
        $direccion_apoderado = (string)($fila["direccion_apoderado"] ?? "");
        $telefono_apoderado = (string)($fila["telefono_apoderado"] ?? "");
        $situacion_especial_apoderado = (string)($fila["situacion_especial_apoderado"] ?? "");
        $fecha_registro = date("Y-m-d H:i:s");
        $estado = "Pendiente";

        try {
            if ($stmtInsert->execute()) {
                $copiados++;
            } else {
                $errores++;
                error_log("Error al copiar matrícula de 1°: " . $stmtInsert->error);
            }
        } catch (Throwable $e) {
            $errores++;
            error_log("Error al copiar matrícula de 1°: " . $e->getMessage());
        }
    }

    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Traspaso completado",
        "copiados" => $copiados,
        "omitidos_duplicados" => $omitidos_duplicados,
        "sin_mapeo" => $sin_mapeo,
        "errores" => $errores,
    ]);
} catch (Throwable $e) {
    $conexion->rollback();

    error_log("Error en traspaso de 1° a próximo año: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Error al traspasar: " . $e->getMessage(),
    ]);
}
