<?php
include '../conexion.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Ayuda en desarrollo

session_start();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: /index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido.");
}

$curso_id = $_POST['curso_id'] ?? null;
$asignatura_id = $_POST['asignatura_id'] ?? null;
$notas = $_POST['notas'] ?? [];

if (!$curso_id || !$asignatura_id || !is_array($notas)) {
    $_SESSION['error'] = "Datos incompletos para guardar notas.";
    header("Location: ver_curso.php?id=$curso_id");
    exit;
}

try {
    $conexion->begin_transaction();

    foreach ($notas as $estudiante_id => $nota_arr) {
        // Limpiar y normalizar las notas
        $valores = [];
        $todasVacias = true;
        for ($i = 1; $i <= 9; $i++) {
            $notaBruta = trim($nota_arr["nota$i"] ?? '');
            $nota = ($notaBruta !== '' && is_numeric($notaBruta)) ? floatval($notaBruta) : null;
            $valores["nota$i"] = $nota;
            if ($nota !== null) $todasVacias = false;
        }

        // Si todas las notas están vacías, elimina el registro de notas si existe
        if ($todasVacias) {
            $stmt = $conexion->prepare("DELETE FROM notas WHERE estudiante_id = ? AND asignatura_id = ?");
            $stmt->bind_param("ii", $estudiante_id, $asignatura_id);
            $stmt->execute();
            $stmt->close();
            continue;
        }

        // Verificar si ya existen notas para este estudiante y asignatura
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM notas WHERE estudiante_id = ? AND asignatura_id = ?");
        $stmt->bind_param("ii", $estudiante_id, $asignatura_id);
        $stmt->execute();
        $stmt->bind_result($existe);
        $stmt->fetch();
        $stmt->close();

        if ($existe) {
            // Actualizar notas
            $stmt = $conexion->prepare("
                UPDATE notas SET 
                    nota1 = ?, nota2 = ?, nota3 = ?, nota4 = ?, nota5 = ?,
                    nota6 = ?, nota7 = ?, nota8 = ?, nota9 = ?
                WHERE estudiante_id = ? AND asignatura_id = ?
            ");
            $stmt->bind_param(
                "dddddddddii",
                $valores['nota1'], $valores['nota2'], $valores['nota3'],
                $valores['nota4'], $valores['nota5'], $valores['nota6'],
                $valores['nota7'], $valores['nota8'], $valores['nota9'],
                $estudiante_id, $asignatura_id
            );
        } else {
            // Obtener el profesor_id para esa asignatura en ese curso
            $stmt_prof = $conexion->prepare("
                SELECT profesor_id FROM curso_profesor 
                WHERE curso_id = ? AND asignatura_id = ?
                LIMIT 1
            ");
            $stmt_prof->bind_param("ii", $curso_id, $asignatura_id);
            $stmt_prof->execute();
            $stmt_prof->bind_result($profesor_id);
            $stmt_prof->fetch();
            $stmt_prof->close();

            if (!$profesor_id) {
                throw new Exception("No se encontró profesor asignado para esta asignatura.");
            }

            // Insertar nuevo registro incluyendo profesor_id
            $stmt = $conexion->prepare("
                INSERT INTO notas (
                    estudiante_id, asignatura_id, profesor_id,
                    nota1, nota2, nota3, nota4, nota5,
                    nota6, nota7, nota8, nota9
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iiiddddddddd",
                $estudiante_id, $asignatura_id, $profesor_id,
                $valores['nota1'], $valores['nota2'], $valores['nota3'],
                $valores['nota4'], $valores['nota5'], $valores['nota6'],
                $valores['nota7'], $valores['nota8'], $valores['nota9']
            );
        }

        $stmt->execute();
        $stmt->close();
    }

    $conexion->commit();
    $_SESSION['mensaje'] = "Notas guardadas correctamente.";
} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['error'] = "Error al guardar notas: " . $e->getMessage();
}

header("Location: ver_curso.php?id=$curso_id");
exit;
?>
