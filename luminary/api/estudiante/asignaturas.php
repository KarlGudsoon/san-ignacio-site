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
  a.id AS asignatura_id,
  a.nombre AS nombre,
  u.nombre AS profesor,
  u.correo AS correo_profesor
FROM curso_asignatura ca
JOIN asignaturas a ON a.id = ca.asignatura_id
JOIN curso_profesor cp ON a.id = cp.asignatura_id AND ca.curso_id = cp.curso_id
JOIN usuarios u ON cp.profesor_id = u.id
WHERE ca.curso_id = ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $curso_id);
$stmt->execute();

$resultado = $stmt->get_result();
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

echo json_encode($datos);
