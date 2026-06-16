<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . '/../../middlewares/auth_editor.php';
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

$id_profesor = $_SESSION["user_id"];
$id_curso_profesor = $_GET["id_curso_profesor"] ?? null;

if (!$id_curso_profesor) {
    echo json_encode(["success" => false, "message" => "Falta id_curso_profesor"]);
    exit;
}

// 1. Estudiantes
$sqlEstudiantes = "SELECT 
        e.id AS estudiante_id,
        m.nombre_estudiante,
        m.apellidos_estudiante,
        m.estudiante_id
    FROM matriculas m
    INNER JOIN estudiantes e ON e.matricula_id = m.id
    INNER JOIN curso_profesor cp ON cp.curso_id = e.curso_id
    WHERE cp.id = ? AND cp.profesor_id = ?
    ORDER BY m.apellidos_estudiante, m.nombre_estudiante";

$stmtEst = $conexion->prepare($sqlEstudiantes);
$stmtEst->bind_param("ii", $id_curso_profesor, $id_profesor);
$stmtEst->execute();
$estudiantesRaw = $stmtEst->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. Evaluaciones
$sqlEvaluaciones = "SELECT 
        e.id,
        e.titulo,
        e.coeficiente2,
        e.fecha_aplicacion,
        te.nombre AS tipo
    FROM evaluaciones e
    INNER JOIN tipo_evaluacion te ON te.id = e.tipo_id
    WHERE e.curso_profesor_id = ? AND e.activo = 1
    ORDER BY e.fecha_aplicacion";

$stmtEv = $conexion->prepare($sqlEvaluaciones);
$stmtEv->bind_param("i", $id_curso_profesor);
$stmtEv->execute();
$evaluaciones = $stmtEv->get_result()->fetch_all(MYSQLI_ASSOC);

// 3. Notas agrupadas por estudiante
$sqlNotas = "SELECT 
        n.estudiante_id,
        n.evaluacion_id,
        n.nota
    FROM notas n
    INNER JOIN evaluaciones e ON n.evaluacion_id = e.id
    WHERE e.curso_profesor_id = ? AND e.activo = 1
    ORDER BY n.evaluacion_id, n.id";

$stmtNotas = $conexion->prepare($sqlNotas);
$stmtNotas->bind_param("i", $id_curso_profesor);
$stmtNotas->execute();
$notasRaw = $stmtNotas->get_result()->fetch_all(MYSQLI_ASSOC);

// Anidar notas dentro de cada estudiante
$notasPorEstudiante = [];
foreach ($notasRaw as $nota) {
    $notasPorEstudiante[$nota['estudiante_id']][] = [
        "evaluacion_id" => $nota['evaluacion_id'],
        "nota" => $nota['nota']
    ];
}

$estudiantes = array_map(function($est) use ($notasPorEstudiante) {
    $est['notas'] = $notasPorEstudiante[$est['estudiante_id']] ?? [];

    // Calcular promedio solo de notas numéricas
    $notasNumericas = array_filter($est['notas'], fn($n) => is_numeric($n['nota']));
    $valores = array_map(fn($n) => (float)$n['nota'], $notasNumericas);

    $est['promedio'] = count($valores) > 0 ? number_format(round(array_sum($valores) / count($valores), 1), 1) : null;
    $est['cantidad_notas'] = count($valores);
    $est['suma_notas'] = number_format(round(array_sum($valores), 1), 1);
    $est['promedio_aproximado'] = count($valores) > 0 ? number_format(array_sum($valores) / count($valores), 2, ',', '') : null;
  
    return $est;
}, $estudiantesRaw);

$totalEvaluaciones = 0;

foreach ($evaluaciones as $ev) {
    $totalEvaluaciones += ($ev['coeficiente2'] == 1) ? 2 : 1;
}

echo json_encode([
    "success" => true,
    "total_notas" => $totalEvaluaciones,
    "evaluaciones" => $evaluaciones,
    "estudiantes" => $estudiantes
]);