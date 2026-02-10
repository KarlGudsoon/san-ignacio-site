<?php
require_once __DIR__ . '/../middlewares/auth_estudiante.php';
require_once __DIR__ . "/../../api/config/db.php";

$estudiante_id = $_SESSION['estudiante_id'] ?? null;
$asignatura_id = $_GET["asignatura_id"] ?? null;

if (!$asignatura_id) {
  http_response_code(400);
  echo json_encode(["error" => "Asignatura requerida"]);
  exit;
}

$sql = "
SELECT 
  nota1, nota2, nota3, nota4, nota5, nota6, nota7, nota8, nota9, x̄
FROM notas
WHERE estudiante_id = ?
AND asignatura_id = ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $estudiante_id, $asignatura_id);
$stmt->execute();

$resultado = $stmt->get_result();
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

echo json_encode($datos);