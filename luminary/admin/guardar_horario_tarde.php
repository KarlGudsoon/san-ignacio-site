<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['horario'])) {
    $horario = $_POST['horario'];

    // Definir bloques manualmente (excluyendo recreo)
    $bloques = [
        "14:00 - 14:45",
        "14:45 - 15:30",
        "15:30 - 16:15",
        "16:30 - 17:15",
        "17:15 - 18:00",
        "18:00 - 18:45",
        "18:45 - 19:30"
    ];

    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    // Borrar horario anterior
    $conexion->query("DELETE FROM horario_tarde");

    // Guardar el nuevo horario
    $stmt = $conexion->prepare("INSERT INTO horario_tarde (dia, bloque, asignatura) VALUES (?, ?, ?)");

    foreach ($horario as $i => $diaData) {
        $bloque = $bloques[$i];
        foreach ($dias as $dia) {
            $asignatura = $diaData[$dia];
            if (!empty($asignatura)) {
                $stmt->bind_param("sss", $dia, $bloque, $asignatura);
                $stmt->execute();
            }
        }
    }

    $stmt->close();
    $conexion->close();

    echo "Horario actualizado correctamente.";
} else {
    echo "Datos inválidos.";
}
