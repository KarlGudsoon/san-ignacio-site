<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Horario Tarde con Select</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
      font-family: sans-serif;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      height: 60px;
    }
    th {
      background-color: #f0f0f0;
    }
    .recreo {
      background-color: #ffeaa7;
      font-weight: bold;
      text-align: center;
    }
    select {
      width: 100%;
      padding: 5px;
    }
  </style>
</head>
<?php
include '../conexion.php';

// Obtener datos del horario ya guardado
$horario_guardado = [];
$res = $conexion->query("SELECT * FROM horario_tarde");
while ($row = $res->fetch_assoc()) {
    $horario_guardado[$row['bloque']][$row['dia']] = $row['asignatura'];
}
?>

<form action="guardar_horario_tarde.php" method="POST">
  <table border="1">
    <thead>
      <tr>
        <th>Bloque</th>
        <th>Lunes</th>
        <th>Martes</th>
        <th>Miércoles</th>
        <th>Jueves</th>
        <th>Viernes</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $bloques = [
          "14:00 - 14:45",
          "14:45 - 15:30",
          "15:30 - 16:15",
          "Recreo (16:15 - 16:30)",
          "16:30 - 17:15",
          "17:15 - 18:00",
          "18:00 - 18:45",
          "18:45 - 19:30"
        ];

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        foreach ($bloques as $i => $bloque) {
          echo "<tr>";
          echo "<td>$bloque</td>";

          if (strpos($bloque, 'Recreo') !== false) {
            echo "<td colspan='5' style='text-align:center; background:#eee;'>RECREO</td>";
          } else {
            foreach ($dias as $dia) {
              $name = "horario[$i][$dia]";
              $valor = $horario_guardado[$bloque][$dia] ?? "";
              echo "<td>
                      <select name=\"$name\">
                        <option value=\"\">-- Seleccionar --</option>";
                        $opciones = ["Lenguaje", "Matemáticas", "Historia", "Ciencias", "Inglés"];
                        foreach ($opciones as $opt) {
                          $selected = ($valor == $opt) ? "selected" : "";
                          echo "<option value=\"$opt\" $selected>$opt</option>";
                        }
              echo "    </select>
                    </td>";
            }
          }

          echo "</tr>";
        }
      ?>
    </tbody>
  </table>
  <br>
  <button type="submit">Guardar Cambios</button>
</form>
