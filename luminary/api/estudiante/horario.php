<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../api/config/db.php";

$curso_id = $_SESSION['curso_id'] ?? null;

if (!$curso_id) {
  echo json_encode([]);
  exit;
}

$sql = "
SELECT 
  h.dia,
  a.nombre AS asignatura,
  b.hora_inicio,
  b.hora_fin,
  b.orden
FROM horarios h
JOIN bloques_horarios b ON b.id = h.bloque_id
JOIN asignaturas a ON a.id = h.asignatura_id
WHERE h.curso_id = ?
ORDER BY 
  FIELD(h.dia,'lunes','martes','miercoles','jueves','viernes'),
  b.orden
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $curso_id);
$stmt->execute();

$resultado = $stmt->get_result();
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

echo json_encode($datos);
