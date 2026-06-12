<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$evaluacion_id = $_POST["evaluacion_id"] ?? null;

if (!$evaluacion_id) {
    echo json_encode(["success" => false, "message" => "Falta evaluacion_id"]);
    exit;
}

try {
    // Obtener coeficiente2 de la evaluación
    $sqlCoef = "SELECT e.coeficiente2, e.curso_profesor_id, cp.curso_id
                FROM evaluaciones e
                INNER JOIN curso_profesor cp ON cp.id = e.curso_profesor_id
                WHERE e.id = ?";
    $stmtCoef = $conexion->prepare($sqlCoef);
    $stmtCoef->bind_param("i", $evaluacion_id);
    $stmtCoef->execute();
    $evaluacion = $stmtCoef->get_result()->fetch_assoc();

    if (!$evaluacion) {
        echo json_encode(["success" => false, "message" => "Evaluación no encontrada"]);
        exit;
    }

    $esCoef2 = (int)$evaluacion['coeficiente2'] === 1;
    $curso_id = $evaluacion['curso_id'];

    // Buscar estudiantes del curso que NO tienen nota en esta evaluación
    $sqlSinNota = "SELECT e.id AS estudiante_id
                   FROM estudiantes e
                   WHERE e.curso_id = ?
                   AND e.id NOT IN (
                       SELECT estudiante_id FROM notas WHERE evaluacion_id = ?
                   )";
    $stmtSinNota = $conexion->prepare($sqlSinNota);
    $stmtSinNota->bind_param("ii", $curso_id, $evaluacion_id);
    $stmtSinNota->execute();
    $estudiantesSinNota = $stmtSinNota->get_result()->fetch_all(MYSQLI_ASSOC);

    $actualizados = 0;

    foreach ($estudiantesSinNota as $est) {
        $est_id = $est['estudiante_id'];
        $veces = $esCoef2 ? 2 : 1;

        for ($i = 0; $i < $veces; $i++) {
            $sqlInsert = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, 'P')";
            $stmtInsert = $conexion->prepare($sqlInsert);
            $stmtInsert->bind_param("ii", $evaluacion_id, $est_id);
            $stmtInsert->execute();
        }
        $actualizados++;
    }

    echo json_encode([
        "success" => true,
        "message" => "Pendientes rellenados correctamente",
        "actualizados" => $actualizados
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}