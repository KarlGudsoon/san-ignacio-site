<?php
require_once __DIR__ . '/../../middlewares/auth_editor.php';
require_once __DIR__ . "/../../config/db.php";

// 1. Indicar al cliente que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Validar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit;
}

// 3. Capturar y sanitizar entradas
$evaluacion_id = filter_input(INPUT_POST, 'evaluacion_id', FILTER_VALIDATE_INT);
$estudiante_id = filter_input(INPUT_POST, 'estudiante_id', FILTER_VALIDATE_INT);
$nota_raw = $_POST['nota'] ?? null;

if (!$evaluacion_id || !$estudiante_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "IDs de evaluación o estudiante no válidos"]);
    exit;
}

// Habilitar excepciones en MySQLi para captura en bloque try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 4. Iniciar Transacción ACID
    $conexion->begin_transaction();

    // Consultar si la evaluación es coeficiente 2
    $sqlCoef = "SELECT coeficiente2 FROM evaluaciones WHERE id = ?";
    $stmtCoef = $conexion->prepare($sqlCoef);
    $stmtCoef->bind_param("i", $evaluacion_id);
    $stmtCoef->execute();
    $evaluacion = $stmtCoef->get_result()->fetch_assoc();

    if (!$evaluacion) {
        throw new Exception("La evaluación especificada no existe", 404);
    }

    $esCoef2 = ((int)$evaluacion['coeficiente2'] === 1);

    // Eliminar filas existentes
    $sqlDelete = "DELETE FROM notas WHERE evaluacion_id = ? AND estudiante_id = ?";
    $stmtDelete = $conexion->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $evaluacion_id, $estudiante_id);
    $stmtDelete->execute();

    // Si la nota viene vacía o compuesta solo de espacios, confirmamos borrado
    if ($nota_raw === null || trim($nota_raw) === '') {
        $conexion->commit();
        echo json_encode(["success" => true, "action" => "deleted"]);
        exit;
    }

    // 5. Validar formato de la nota (Conceptos permitidos o Números 1.0 a 7.0)
    $conceptos_permitidos = ['L', 'ML', 'NL', 'P', 'E'];
    $nota_str = strtoupper(trim($nota_raw));

    if (in_array($nota_str, $conceptos_permitidos, true)) {
        // Es una nota conceptual válida
        $nota_final = $nota_str;
    } else {
        // Si no es un concepto, debe ser un número válido entre 1.0 y 7.0
        $nota_num = filter_var($nota_raw, FILTER_VALIDATE_FLOAT);
        if ($nota_num === false || $nota_num < 1.0 || $nota_num > 7.0) {
            throw new Exception("La nota debe ser una calificación conceptual (L, ML, NL, P, E) o un número entre 1.0 y 7.0", 400);
        }
        $nota_final = number_format($nota_num, 1, '.', '');
    }

    // 6. Insertar nota (1 o 2 filas según coeficiente)
    $sqlInsert = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->bind_param("iis", $evaluacion_id, $estudiante_id, $nota_final);
    $stmtInsert->execute();

    if ($esCoef2) {
        $stmtInsert->execute();
    }

    // Confirmar cambios en BD
    $conexion->commit();

    echo json_encode(["success" => true, "action" => "saved"]);

} catch (Exception $e) {
    // Si algo falla, deshacer el borrado y cualquier inserción
    $conexion->rollback();

    $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    http_response_code($code);

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}