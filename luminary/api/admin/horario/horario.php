<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../../api/config/db.php";

$curso_id = $_GET['curso_id'] ?? null;

if (!$curso_id) {
  echo json_encode([]);
  exit;
}

//
// 1️⃣ Obtener jornada del curso
//
$stmt = $conexion->prepare("SELECT jornada FROM cursos WHERE id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
  echo json_encode([]);
  exit;
}

$jornada = $res["jornada"];

//
// 2️⃣ Traer solo bloques de esa jornada
//
$sql = "
SELECT 
  b.id AS bloque_id,
  b.hora_inicio,
  b.hora_fin,
  b.orden,
  h.dia,
  h.asignatura_id,
  a.nombre AS asignatura
FROM bloques_horarios b
LEFT JOIN horarios h 
  ON h.bloque_id = b.id AND h.curso_id = ?
LEFT JOIN asignaturas a
  ON h.asignatura_id = a.id
WHERE b.jornada = ?
ORDER BY b.orden
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("is", $curso_id, $jornada);
$stmt->execute();

$resultado = $stmt->get_result();
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

echo json_encode($datos);