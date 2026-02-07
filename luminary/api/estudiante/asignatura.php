<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../api/config/db.php";

$id = $_GET['id'] ?? null;
$curso_id = $_SESSION['curso_id'] ?? null;

if (!$id || !$curso_id) {
  http_response_code(400);
  echo json_encode(["error" => "Datos inválidos"]);
  exit;
}

$sql = "
SELECT 
  a.id,
  a.nombre AS asignatura,
  u.nombre AS profesor
FROM curso_asignatura ca
JOIN asignaturas a ON a.id = ca.asignatura_id
JOIN curso_profesor cp ON cp.asignatura_id = a.id AND cp.curso_id = ca.curso_id
JOIN usuarios u ON u.id = cp.profesor_id
WHERE ca.asignatura_id = ?
  AND ca.curso_id = ?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id, $curso_id);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
  http_response_code(404);
  echo json_encode(["error" => "Asignatura no encontrada"]);
  exit;
}

echo json_encode($data);
