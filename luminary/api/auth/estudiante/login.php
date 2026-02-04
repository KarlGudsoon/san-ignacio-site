<?php
session_start();
require_once __DIR__ . "/../../config/db.php";

// 1️⃣ Obtener y normalizar RUT
$rut = $_POST['rut'] ?? '';
$rut = str_replace(['.'], '', $rut);
$rut = trim($rut);

if ($rut === '') {
    header("Location: /luminary/?error=1");
    exit;
}

// 2️⃣ Consulta
$sql = "
SELECT 
    e.id AS estudiante_id,
    m.nombre_estudiante,
    m.apellidos_estudiante,
    m.rut_estudiante,
    e.curso_id,
    c.nivel AS curso_nivel,
    c.letra AS curso_letra
FROM estudiantes e
INNER JOIN matriculas m ON m.id = e.matricula_id
INNER JOIN cursos c ON c.id = e.curso_id
WHERE m.rut_estudiante = ?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $rut);
$stmt->execute();

$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

// 3️⃣ Validación
if (!$estudiante) {
    header("Location: /luminary/?error=1");
    exit;
}

// 4️⃣ Guardar sesión (forma correcta)
$_SESSION['estudiante_id'] = $estudiante['estudiante_id'];
$_SESSION['estudiante_nombre'] = $estudiante['nombre_estudiante'];
$_SESSION['estudiante_apellidos'] = $estudiante['apellidos_estudiante'];
$_SESSION['estudiante_rut'] = $estudiante['rut_estudiante'];
$_SESSION['curso_id'] = $estudiante['curso_id'];
$_SESSION['curso_nivel'] = $estudiante['curso_nivel'];
$_SESSION['curso_letra'] = $estudiante['curso_letra'];
$_SESSION['curso'] = $estudiante['curso_nivel'] . " Nivel " . $estudiante['curso_letra'];
$_SESSION['last_activity'] = time();

// 5️⃣ Redirección
header("Location: /luminary/estudiante/dashboard");
exit;
