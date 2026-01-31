<?php
header("Content-Type: application/json");
require "../config/db.php";

$curso_id = $_GET['id'] ?? null;

if (!$curso_id || !is_numeric($curso_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Curso no válido"]);
    exit;
}

/* ===== Curso ===== */
$stmt = $conexion->prepare("
    SELECT c.id, c.nivel, c.letra, u.nombre, u.correo
    FROM cursos c
    INNER JOIN usuarios u ON c.profesor_jefe_id = u.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    http_response_code(404);
    echo json_encode(["error" => "Curso no encontrado"]);
    exit;
}

/* ===== Jornada ===== */
function asignarJornadaPorCurso($curso) {
    $jornadas = [
        'Mañana' => [1,4,5],
        'Tarde'  => [2,6,7],
        'Noche'  => [3,8,9]
    ];

    foreach ($jornadas as $jornada => $cursos) {
        if (in_array($curso, $cursos)) {
            return $jornada;
        }
    }
    return 'Sin información';
}

$jornada = asignarJornadaPorCurso($curso_id);

$horarios = [
    "Mañana" => ["8:15 a 13:30", "/assets/icon/meteocons--sunset-fill.svg"],
    "Tarde"  => ["14:00 a 18:00", "/assets/icon/line-md--sunny-filled-loop.svg"],
    "Noche"  => ["18:00 a 23:00", "/assets/icon/line-md--moon-filled-alt-loop.svg"],
];

$horario = $horarios[$jornada][0] ?? "Sin horario";
$icono   = $horarios[$jornada][1] ?? null;

/* ===== Asignaturas ===== */
$stmt = $conexion->prepare("
    SELECT 
        ca.asignatura_id,
        a.nombre AS asignatura,
        u.nombre AS profesor
    FROM curso_asignatura ca
    INNER JOIN asignaturas a ON ca.asignatura_id = a.id
    LEFT JOIN curso_profesor cp 
        ON cp.curso_id = ca.curso_id 
        AND cp.asignatura_id = ca.asignatura_id
    LEFT JOIN usuarios u ON u.id = cp.profesor_id
    WHERE ca.curso_id = ?
");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$asignaturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ===== Respuesta API ===== */
echo json_encode([
    "curso" => $curso,
    "jornada" => $jornada,
    "horario" => $horario,
    "icono" => $icono,
    "asignaturas" => $asignaturas
]);
