<?php
session_start();
require "../conexion.php";

$rut = $_POST['rut'] ?? '';
$rut = str_replace(['.'], '', $rut);

if (!$rut) {
  header("Location: ../?error=1");
  exit;
}

$sql = "
  SELECT 
    e.id AS estudiante_id,
    e.curso_id,
    m.id AS matricula_id,
    m.rut_estudiante
  FROM estudiantes e
  INNER JOIN matriculas m ON e.matricula_id = m.id
  WHERE m.rut_estudiante = ?
  LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $rut);
$stmt->execute();
$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

if ($estudiante) {
  $_SESSION['estudiante_id'] = $estudiante['estudiante_id'];
  $_SESSION['matricula_id']  = $estudiante['matricula_id'];
  $_SESSION['curso_id']      = $estudiante['curso_id'];
  $_SESSION['rut']           = $estudiante['rut_estudiante'];

  header("Location: ./dashboard");
  exit;
}

header("Location: ../?error=2");
exit;
