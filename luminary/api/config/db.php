<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conexion = new mysqli(
  "localhost",
  "root",
  "",
  "luminary"
);

$conexion->set_charset("utf8");
?>