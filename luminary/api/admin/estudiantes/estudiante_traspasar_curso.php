<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
header("Content-Type: application/json");

// Validar sesión
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);
    exit;
}

// Obtener datos del POST
$estudiante_id = $_POST["estudiante_id"] ?? null;
$nuevo_curso_id = $_POST["nuevo_curso_id"] ?? null;

error_log("API Traspaso - estudiante_id: $estudiante_id, nuevo_curso_id: $nuevo_curso_id");

if (!$estudiante_id || !$nuevo_curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos: estudiante_id y nuevo_curso_id son requeridos"
    ]);
    exit;
}

// Validar que el estudiante existe
$sqlEstudiante = "SELECT id, curso_id FROM estudiantes WHERE id = ?";
$stmtEstudiante = $conexion->prepare($sqlEstudiante);
$stmtEstudiante->bind_param("i", $estudiante_id);
$stmtEstudiante->execute();
$resultEstudiante = $stmtEstudiante->get_result();

if ($resultEstudiante->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no encontrado"
    ]);
    exit;
}

$estudiante = $resultEstudiante->fetch_assoc();
$curso_actual = $estudiante["curso_id"];

error_log("Estudiante encontrado, curso actual: $curso_actual");

if ($curso_actual == $nuevo_curso_id) {
    echo json_encode([
        "success" => false,
        "message" => "El estudiante ya está en ese curso"
    ]);
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // 1. Obtener todas las notas del estudiante en el curso actual
    $sqlNotas = "SELECT n.id, n.nota, n.evaluacion_id, e.titulo, e.fecha_aplicacion, e.tipo_id,
                        cp.asignatura_id, cp.profesor_id
                 FROM notas n
                 INNER JOIN evaluaciones e ON n.evaluacion_id = e.id
                 INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
                 WHERE n.estudiante_id = ? AND cp.curso_id = ?";

    $stmtNotas = $conexion->prepare($sqlNotas);
    $stmtNotas->bind_param("ii", $estudiante_id, $curso_actual);
    $stmtNotas->execute();
    $resultNotas = $stmtNotas->get_result();

    $notas_traspasadas = 0;
    $evaluaciones_creadas = 0;

    while ($nota = $resultNotas->fetch_assoc()) {
        // 2. Buscar si existe una evaluación equivalente en el nuevo curso
        // (mismo profesor y misma asignatura)
        $sqlEvalEquivalente = "SELECT e.id
                              FROM evaluaciones e
                              INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
                              WHERE cp.curso_id = ? AND cp.profesor_id = ? AND cp.asignatura_id = ?
                              AND e.titulo = ? AND e.fecha_aplicacion = ? AND e.tipo_id = ?";

        $stmtEvalEquiv = $conexion->prepare($sqlEvalEquivalente);
        $stmtEvalEquiv->bind_param("iiisss", $nuevo_curso_id, $nota['profesor_id'],
                                  $nota['asignatura_id'], $nota['titulo'],
                                  $nota['fecha_aplicacion'], $nota['tipo_id']);
        $stmtEvalEquiv->execute();
        $resultEvalEquiv = $stmtEvalEquiv->get_result();

        if ($resultEvalEquiv->num_rows > 0) {
            // Existe evaluación equivalente, copiar la nota
            $evaluacion_equivalente = $resultEvalEquiv->fetch_assoc();

            // Verificar si ya existe una nota para esta evaluación
            $sqlCheckNota = "SELECT id FROM notas WHERE evaluacion_id = ? AND estudiante_id = ?";
            $stmtCheckNota = $conexion->prepare($sqlCheckNota);
            $stmtCheckNota->bind_param("ii", $evaluacion_equivalente['id'], $estudiante_id);
            $stmtCheckNota->execute();
            $resultCheckNota = $stmtCheckNota->get_result();

            if ($resultCheckNota->num_rows === 0) {
                // Insertar nueva nota
                $sqlInsertNota = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
                $stmtInsertNota = $conexion->prepare($sqlInsertNota);
                $stmtInsertNota->bind_param("iid", $evaluacion_equivalente['id'], $estudiante_id, $nota['nota']);
                $stmtInsertNota->execute();
                $notas_traspasadas++;
            }
        } else {
            // No existe evaluación equivalente, crear una nueva
            // Primero verificar si existe curso_profesor para el nuevo curso
            $sqlCheckCP = "SELECT id FROM curso_profesor
                          WHERE curso_id = ? AND profesor_id = ? AND asignatura_id = ?";
            $stmtCheckCP = $conexion->prepare($sqlCheckCP);
            $stmtCheckCP->bind_param("iii", $nuevo_curso_id, $nota['profesor_id'], $nota['asignatura_id']);
            $stmtCheckCP->execute();
            $resultCheckCP = $stmtCheckCP->get_result();

            if ($resultCheckCP->num_rows > 0) {
                $cp_equivalente = $resultCheckCP->fetch_assoc();

                // Crear nueva evaluación
                $sqlInsertEval = "INSERT INTO evaluaciones (titulo, fecha_aplicacion, tipo_id, curso_profesor_id)
                                 VALUES (?, ?, ?, ?)";
                $stmtInsertEval = $conexion->prepare($sqlInsertEval);
                $stmtInsertEval->bind_param("sssi", $nota['titulo'], $nota['fecha_aplicacion'],
                                           $nota['tipo_id'], $cp_equivalente['id']);
                $stmtInsertEval->execute();
                $nueva_evaluacion_id = $conexion->insert_id;

                // Insertar la nota para la nueva evaluación
                $sqlInsertNota = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
                $stmtInsertNota = $conexion->prepare($sqlInsertNota);
                $stmtInsertNota->bind_param("iid", $nueva_evaluacion_id, $estudiante_id, $nota['nota']);
                $stmtInsertNota->execute();

                $evaluaciones_creadas++;
                $notas_traspasadas++;
            }
        }
    }

    // 3. Actualizar el curso del estudiante
    $sqlUpdateEstudiante = "UPDATE estudiantes SET curso_id = ? WHERE id = ?";
    $stmtUpdateEstudiante = $conexion->prepare($sqlUpdateEstudiante);
    $stmtUpdateEstudiante->bind_param("ii", $nuevo_curso_id, $estudiante_id);
    $stmtUpdateEstudiante->execute();

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Estudiante traspasado exitosamente",
        "notas_traspasadas" => $notas_traspasadas,
        "evaluaciones_creadas" => $evaluaciones_creadas
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();

    error_log("Error en traspaso: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Error al traspasar estudiante: " . $e->getMessage()
    ]);
}
?>