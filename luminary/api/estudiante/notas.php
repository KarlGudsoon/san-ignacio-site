<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../api/config/db.php";

$estudiante_id = $_SESSION['estudiante_id'] ?? null;

if (!$estudiante_id) {
  http_response_code(400);
  echo json_encode(["error" => "Estudiante requerido"]);
  exit;
}

$sql = "
SELECT 
  nota1, nota2, nota3, nota4, nota5, nota6, nota7, nota8, nota9, x̄, asignatura_id
FROM notas
WHERE estudiante_id = ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();

$resultado = $stmt->get_result();
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

echo json_encode($datos);