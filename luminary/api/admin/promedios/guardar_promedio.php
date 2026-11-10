<?php
require_once __DIR__ . "/../../middlewares/auth_admin2.php";
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$estudiante_id = intval($_POST["estudiante_id"] ?? 0);
$curso_profesor_id = intval($_POST["curso_profesor_id"] ?? 0);
$promedio = trim($_POST["promedio_anual"] ?? $_POST["promedio"] ?? "");

if ($estudiante_id <= 0 || $curso_profesor_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Parámetros inválidos"
    ]);
    exit;
}

// ===== 1. Validar y normalizar ANTES de tocar la base de datos =====
$valorGuardar = null; // null = sin promedio (se elimina)

if ($promedio !== "") {
    if (strtoupper($promedio) === "P") {
        $valorGuardar = "P";
    } else {
        if (!is_numeric($promedio)) {
            echo json_encode([
                "success" => false,
                "message" => "El promedio debe ser numérico o 'P'"
            ]);
            exit;
        }

        $num = (float)$promedio;

        if ($num < 1.0 || $num > 7.0) {
            echo json_encode([
                "success" => false,
                "message" => "El promedio debe estar entre 1.0 y 7.0"
            ]);
            exit;
        }

        // Redondeo a 1 decimal: centésima >= 5 sube (6.25 -> 6.3)
        $valorGuardar = number_format(
            floor($num * 10 + 0.5 + 1e-9) / 10,
            1
        );
    }
}

// ===== 2. Eliminar el registro previo (si existe) =====
$sqlDelete = "DELETE FROM promedios_asignaturas 
              WHERE estudiante_id = ? AND curso_profesor_id = ?";
$stmtDelete = $conexion->prepare($sqlDelete);

if (!$stmtDelete) {
    echo json_encode([
        "success" => false,
        "message" => "Error al preparar la consulta"
    ]);
    exit;
}

$stmtDelete->bind_param("ii", $estudiante_id, $curso_profesor_id);
$stmtDelete->execute();
$stmtDelete->close();

// Si el promedio está vacío, solo se eliminó y listo
if ($valorGuardar === null) {
    echo json_encode(["success" => true, "promedio_anual" => null]);
    exit;
}

// ===== 3. Insertar el nuevo promedio anual =====
$sqlInsert = "INSERT INTO promedios_asignaturas 
                (estudiante_id, curso_profesor_id, promedio_anual) 
              VALUES (?, ?, ?)";
$stmtInsert = $conexion->prepare($sqlInsert);

if (!$stmtInsert) {
    echo json_encode([
        "success" => false,
        "message" => "Error al preparar la consulta"
    ]);
    exit;
}

$stmtInsert->bind_param("iis", $estudiante_id, $curso_profesor_id, $valorGuardar);

if (!$stmtInsert->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "No se pudo guardar el promedio"
    ]);
    $stmtInsert->close();
    exit;
}

$stmtInsert->close();

echo json_encode([
    "success" => true,
    "promedio_anual" => $valorGuardar
]);
