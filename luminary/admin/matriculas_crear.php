<?php
session_start();
require_once '../conexion.php'; // Ajusta la ruta si es necesario

// Verificar que los datos vienen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Método no permitido.");
}

// Sanitizar datos
function limpiar($campo) {
    return htmlspecialchars(trim($campo));
}

// Datos del estudiante
$nombre_estudiante               = limpiar($_POST['nombre_estudiante']);
$apellidos_estudiante            = limpiar($_POST['apellidos_estudiante']);
$fecha_nacimiento                = limpiar($_POST['fecha_nacimiento']);
$rut_estudiante                  = limpiar($_POST['rut_estudiante']);
$serie_carnet_estudiante         = limpiar($_POST['serie_carnet_estudiante']);
$etnia_estudiante                = limpiar($_POST['etnia_estudiante']);
$direccion_estudiante            = limpiar($_POST['direccion_estudiante']);
$correo_estudiante               = limpiar($_POST['correo_estudiante']);
$jornada_preferida               = '';
$telefono_estudiante             = limpiar($_POST['telefono_estudiante']);
$hijos_estudiante                = limpiar($_POST['hijos_estudiante']);
$situacion_especial_estudiante   = limpiar($_POST['situacion_especial_estudiante']);
$programa_estudiante             = limpiar($_POST['programa_estudiante']);
$curso_preferido                 = (int)limpiar($_POST['curso_preferido']); // Convertir a entero

// Datos del apoderado
$nombre_apoderado                = limpiar($_POST['nombre_apoderado']);
$rut_apoderado                   = limpiar($_POST['rut_apoderado']);
$parentezco_apoderado            = limpiar($_POST['parentezco_apoderado']);
$direccion_apoderado             = limpiar($_POST['direccion_apoderado']);
$telefono_apoderado              = limpiar($_POST['telefono_apoderado']);

// Función para asignar jornada por curso
function asignarJornadaPorCurso($curso) {
    // Definir qué cursos van en cada jornada
    $jornadas = [
        'Mañana' => [1, 4, 5],
        'Tarde' => [2, 6, 7],
        'Noche' => [3, 8, 9]
    ];
    
    // Buscar el curso en cada jornada
    foreach ($jornadas as $jornada => $cursos) {
        if (in_array($curso, $cursos)) {
            return $jornada;
        }
    }
    
    return 'Sin información'; // Jornada por defecto si no se encuentra
}

// Determinar jornada preferida
if (isset($_POST['jornada_preferida']) && !empty($_POST['jornada_preferida'])) {
    $jornada_preferida = limpiar($_POST['jornada_preferida']);
} else {
    // Si no, asignar según el curso
    $jornada_preferida = asignarJornadaPorCurso($curso_preferido);
}

// Consulta SQL
$sql = "INSERT INTO matriculas (
            nombre_estudiante,
            apellidos_estudiante,
            fecha_nacimiento,
            rut_estudiante,
            serie_carnet_estudiante,
            etnia_estudiante,
            direccion_estudiante,
            correo_estudiante,
            jornada_preferida,
            telefono_estudiante,
            hijos_estudiante,
            situacion_especial_estudiante,
            programa_estudiante,
            curso_preferido,
            nombre_apoderado,
            rut_apoderado,
            parentezco_apoderado,
            direccion_apoderado,
            telefono_apoderado, 
            fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);

// Verificar si hubo error en la preparación
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Nota: hijos_estudiante debería ser 'i' (entero) no 's' (string) si es número
$hijos_estudiante_int = (int)$hijos_estudiante;

$stmt->bind_param(
    "ssssssssssissssssss",
    $nombre_estudiante,
    $apellidos_estudiante,
    $fecha_nacimiento,
    $rut_estudiante,
    $serie_carnet_estudiante,
    $etnia_estudiante,
    $direccion_estudiante,
    $correo_estudiante,
    $jornada_preferida,
    $telefono_estudiante,
    $hijos_estudiante_int,  // Usar la variable convertida a entero
    $situacion_especial_estudiante,
    $programa_estudiante,  
    $curso_preferido,
    $nombre_apoderado,
    $rut_apoderado,
    $parentezco_apoderado,
    $direccion_apoderado,
    $telefono_apoderado
);

if ($stmt->execute()) {
    // 🔵 Redirigir con éxito
    header("Location: matriculas.php");
    exit();
} else {
    // 🔴 Mostrar error
    echo "Error al guardar matrícula: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
