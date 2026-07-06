
<?php
require_once __DIR__ . '/../../middlewares/auth_admin2.php';
require_once __DIR__ . "/../../config/db.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header("Content-Type: application/json");



if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

if (!isset($_GET['id'])) {
    exit("ID no recibido");
}

$id_formulario = intval($_GET['id']);

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // 1️⃣ Obtener datos de la solicitud de matrícula
    $sqlFormulario = "SELECT * FROM matriculas_formulario WHERE id = ? LIMIT 1";
    $stmtFormulario = $conexion->prepare($sqlFormulario);
    $stmtFormulario->bind_param("i", $id_formulario);
    $stmtFormulario->execute();
    $result = $stmtFormulario->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Matrícula no encontrada");
    }

    $m = $result->fetch_assoc();
    $stmtFormulario->close();

    // ✅ Verificar si ya existe un estudiante con el mismo RUT
    $stmtRut = $conexion->prepare("SELECT id FROM matriculas WHERE rut_estudiante = ? LIMIT 1");
    $stmtRut->bind_param("s", $m['rut_estudiante']);
    $stmtRut->execute();
    $stmtRut->store_result();

    if ($stmtRut->num_rows > 0) {
        $stmtRut->close();
        $conexion->rollback();
        echo json_encode([
            "success" => false,
            "message" => "Ya existe un estudiante registrado con el RUT {$m['rut_estudiante']}"
        ]);
        exit;
    }
    $stmtRut->close();

    // 2️⃣ Insertar matricula en la tabla matriculas
    $sqlMatricula = "INSERT INTO matriculas (
        nombre_estudiante,
        apellidos_estudiante,
        fecha_nacimiento,
        rut_estudiante,
        serie_carnet_estudiante,
        etnia_estudiante,
        direccion_estudiante,
        correo_estudiante,
        telefono_estudiante,
        hijos_estudiante,
        situacion_especial_estudiante,
        programa_estudiante,
        nombre_apoderado,
        rut_apoderado,
        parentezco_apoderado,
        direccion_apoderado,
        telefono_apoderado,
        situacion_especial_apoderado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtMatricula = $conexion->prepare($sqlMatricula);
    $stmtMatricula->bind_param(
        "ssssssssssssssssss",
        $m['nombre_estudiante'],
        $m['apellidos_estudiante'],
        $m['fecha_nacimiento'],
        $m['rut_estudiante'],
        $m['serie_carnet_estudiante'],
        $m['etnia_estudiante'],
        $m['direccion_estudiante'],
        $m['correo_estudiante'],
        $m['telefono_estudiante'],
        $m['hijos_estudiante'],
        $m['situacion_especial_estudiante'],
        $m['programa_estudiante'],
        $m['nombre_apoderado'],
        $m['rut_apoderado'],
        $m['parentezco_apoderado'],
        $m['direccion_apoderado'],
        $m['telefono_apoderado'],
        $m['situacion_especial_apoderado']
    );
    $stmtMatricula->execute();
    $id_nueva_matricula = $conexion->insert_id;
    $stmtMatricula->close();

    // 3️⃣ Insertar en la tabla estudiantes con el ID de la matrícula y el curso preferido
    $stmtEstudiante = $conexion->prepare("INSERT INTO estudiantes (matricula_id, curso_id) VALUES (?, ?)");
    $stmtEstudiante->bind_param("ii", $id_nueva_matricula, $m['curso_preferido']); // ✅ Variables correctas
    $stmtEstudiante->execute();
    $id_estudiante = $conexion->insert_id;
    $stmtEstudiante->close();

    // 4️⃣ Actualizar matricula con el estudiante_id generado
    $stmtUpdate = $conexion->prepare("UPDATE matriculas SET estudiante_id = ? WHERE id = ?");
    $stmtUpdate->bind_param("ii", $id_estudiante, $id_nueva_matricula); // ✅ Variables correctas
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // 5️⃣ Marcar el formulario original como procesado
    $stmtFormularioUpdate = $conexion->prepare("UPDATE matriculas_formulario SET estado = 'Activa' WHERE id = ?");
    $stmtFormularioUpdate->bind_param("i", $id_formulario);
    $stmtFormularioUpdate->execute();
    $stmtFormularioUpdate->close();

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Matrícula activada correctamente",
        "id_matricula" => $id_nueva_matricula,
        "id_estudiante" => $id_estudiante
    ]);


} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage(), // ✅ Muestra el error real
        "linea" => $e->getLine(),                   // ✅ Línea donde falló
        "archivo" => basename($e->getFile())        // ✅ Archivo donde falló
    ]);
}

