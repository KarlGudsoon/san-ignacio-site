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
$estudiante_id = $_POST["estudianteId"] ?? null;
$curso_actual_id = $_POST["cursoActual"] ?? null;
$curso_nuevo_id = $_POST["cursoNuevo"] ?? null;

error_log("API Traspaso - estudiante_id: $estudiante_id, curso_actual: $curso_actual_id, curso_nuevo: $curso_nuevo_id");

// Validación básica
if (!$estudiante_id || !$curso_actual_id || !$curso_nuevo_id) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan datos obligatorios: estudianteId, cursoActual y cursoNuevo son requeridos"
    ]);
    exit;
}

// Validar que el estudiante existe
$sql_estudiante = "SELECT id, curso_id FROM estudiantes WHERE id = ?";
$stmt_estudiante = $conexion->prepare($sql_estudiante);
$stmt_estudiante->bind_param("i", $estudiante_id);
$stmt_estudiante->execute();
$result_estudiante = $stmt_estudiante->get_result();

if ($result_estudiante->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Estudiante no encontrado"
    ]);
    exit;
}

$estudiante = $result_estudiante->fetch_assoc();
$curso_actual_real = $estudiante["curso_id"];

error_log("Estudiante encontrado, curso actual real: $curso_actual_real");

// Validar que el curso actual coincida
if ($curso_actual_real != $curso_actual_id) {
    echo json_encode([
        "success" => false,
        "message" => "El curso actual no coincide con el registro del estudiante"
    ]);
    exit;
}

// Validar que el curso nuevo existe
$sql_validar_curso_nuevo = "SELECT id FROM cursos WHERE id = ?";
$stmt_validar_curso_nuevo = $conexion->prepare($sql_validar_curso_nuevo);
$stmt_validar_curso_nuevo->bind_param("i", $curso_nuevo_id);
$stmt_validar_curso_nuevo->execute();
if ($stmt_validar_curso_nuevo->get_result()->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "El curso nuevo no existe"
    ]);
    exit;
}

if ($curso_actual_real == $curso_nuevo_id) {
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
    $sql_notas = "SELECT n.evaluacion_id, MIN(n.nota) as nota, e.titulo, e.fecha_aplicacion, 
                     e.tipo_id, e.coeficiente2, cp.asignatura_id, cp.profesor_id
              FROM notas n
              INNER JOIN evaluaciones e ON n.evaluacion_id = e.id
              INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
              WHERE n.estudiante_id = ? AND cp.curso_id = ?
              GROUP BY n.evaluacion_id, e.titulo, e.fecha_aplicacion, 
                       e.tipo_id, e.coeficiente2, cp.asignatura_id, cp.profesor_id";

    $stmt_notas = $conexion->prepare($sql_notas);
    $stmt_notas->bind_param("ii", $estudiante_id, $curso_actual_id);
    $stmt_notas->execute();
    $result_notas = $stmt_notas->get_result();

    $notas_traspasadas = 0;
    $evaluaciones_creadas = 0;
    $notas_eliminadas = 0;

    while ($nota = $result_notas->fetch_assoc()) {
        // 2. Buscar si existe una evaluación equivalente en el nuevo curso
        // (mismo profesor y misma asignatura)
        $sql_eval_equivalente = "SELECT e.id
                                FROM evaluaciones e
                                INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
                                WHERE cp.curso_id = ? AND cp.profesor_id = ? AND cp.asignatura_id = ?
                                AND e.titulo = ? AND e.fecha_aplicacion = ? AND e.tipo_id = ?";

        $stmt_eval_equiv = $conexion->prepare($sql_eval_equivalente);
        $stmt_eval_equiv->bind_param("iiisss", $curso_nuevo_id, $nota['profesor_id'],
                                    $nota['asignatura_id'], $nota['titulo'],
                                    $nota['fecha_aplicacion'], $nota['tipo_id']);
        $stmt_eval_equiv->execute();
        $result_eval_equiv = $stmt_eval_equiv->get_result();

        if ($result_eval_equiv->num_rows > 0) {
                $evaluacion_equivalente = $result_eval_equiv->fetch_assoc();

                // Contar cuántas filas existen ya para esta evaluación
                $sql_check_nota = "SELECT COUNT(*) as filas FROM notas WHERE evaluacion_id = ? AND estudiante_id = ?";
                $stmt_check_nota = $conexion->prepare($sql_check_nota);
                $stmt_check_nota->bind_param("ii", $evaluacion_equivalente['id'], $estudiante_id);
                $stmt_check_nota->execute();
                $filas_existentes = (int) $stmt_check_nota->get_result()->fetch_assoc()['filas'];

                $filas_necesarias = $nota['coeficiente2'] == 1 ? 2 : 1;

                // Insertar solo las filas que faltan
                for ($i = $filas_existentes; $i < $filas_necesarias; $i++) {
                    $sql_insert_nota = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
                    $stmt_insert_nota = $conexion->prepare($sql_insert_nota);
                    $stmt_insert_nota->bind_param("iis", $evaluacion_equivalente['id'], $estudiante_id, $nota['nota']);
                    $stmt_insert_nota->execute();
                    $notas_traspasadas++;
                }
            } else {
            // No existe evaluación equivalente, crear una nueva
            // Primero verificar si existe curso_profesor para el nuevo curso
            $sql_check_cp = "SELECT id FROM curso_profesor
                            WHERE curso_id = ? AND profesor_id = ? AND asignatura_id = ?";
            $stmt_check_cp = $conexion->prepare($sql_check_cp);
            $stmt_check_cp->bind_param("iii", $curso_nuevo_id, $nota['profesor_id'], $nota['asignatura_id']);
            $stmt_check_cp->execute();
            $result_check_cp = $stmt_check_cp->get_result();

            if ($result_check_cp->num_rows > 0) {
                $cp_equivalente = $result_check_cp->fetch_assoc();

                // Crear nueva evaluación
                $sql_insert_eval = "INSERT INTO evaluaciones (titulo, fecha_aplicacion, coeficiente2, tipo_id, curso_profesor_id, activo)
                                   VALUES (?, ?, ?, ?, ?, 0)";
                $stmt_insert_eval = $conexion->prepare($sql_insert_eval);
                $stmt_insert_eval->bind_param("ssisi", $nota['titulo'], $nota['fecha_aplicacion'],
                                             $nota['coeficiente2'], $nota['tipo_id'], $cp_equivalente['id']);
                $stmt_insert_eval->execute();
                $nueva_evaluacion_id = $conexion->insert_id;

                $sql_insert_nota = "INSERT INTO notas (evaluacion_id, estudiante_id, nota) VALUES (?, ?, ?)";
                $stmt_insert_nota = $conexion->prepare($sql_insert_nota);
                $stmt_insert_nota->bind_param("iis", $nueva_evaluacion_id, $estudiante_id, $nota['nota']);
                $stmt_insert_nota->execute();

                // Si es coeficiente 2, insertar segunda fila
                if ($nota['coeficiente2'] == 1) {
                    $stmt_insert_nota = $conexion->prepare($sql_insert_nota);
                    $stmt_insert_nota->bind_param("iis", $nueva_evaluacion_id, $estudiante_id, $nota['nota']);
                    $stmt_insert_nota->execute();
                }

                $evaluaciones_creadas++;
                $notas_traspasadas++;
            }
        }
    }

    // 3. Actualizar el curso del estudiante
    $sql_update_estudiante = "UPDATE estudiantes SET curso_id = ? WHERE id = ?";
    $stmt_update_estudiante = $conexion->prepare($sql_update_estudiante);
    $stmt_update_estudiante->bind_param("ii", $curso_nuevo_id, $estudiante_id);
    $stmt_update_estudiante->execute();

    $sql_update_matricula = "UPDATE matriculas SET curso_preferido = ? WHERE estudiante_id = ?";
    $stmt_update_matricula = $conexion->prepare($sql_update_matricula);
    $stmt_update_matricula->bind_param("ii", $curso_nuevo_id, $estudiante_id);
    $stmt_update_matricula->execute();

    // 4. Eliminar todas las notas del curso anterior
    $sql_eliminar_notas = "DELETE n FROM notas n
                          INNER JOIN evaluaciones e ON n.evaluacion_id = e.id
                          INNER JOIN curso_profesor cp ON e.curso_profesor_id = cp.id
                          WHERE n.estudiante_id = ? AND cp.curso_id = ?";
    $stmt_eliminar_notas = $conexion->prepare($sql_eliminar_notas);
    $stmt_eliminar_notas->bind_param("ii", $estudiante_id, $curso_actual_id);
    $stmt_eliminar_notas->execute();
    $notas_eliminadas = $stmt_eliminar_notas->affected_rows;

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Estudiante traspasado exitosamente",
        "notas_traspasadas" => $notas_traspasadas,
        "evaluaciones_creadas" => $evaluaciones_creadas,
        "notas_eliminadas" => $notas_eliminadas
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();

    error_log("Error en traspaso de estudiante: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Error al traspasar estudiante: " . $e->getMessage()
    ]);
}
?>