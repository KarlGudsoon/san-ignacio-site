<?php
require_once '../conexion.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Validar ID
if (!isset($_GET['id'])) exit("ID inválido");
$id = intval($_GET['id']);

// Obtener datos
$query = "
    SELECT m.*, c.nivel, c.letra 
    FROM matriculas m
    LEFT JOIN cursos c ON m.curso_preferido = c.id
    WHERE m.id = $id
    LIMIT 1
";
$result = $conexion->query($query);

if ($result->num_rows === 0) exit("Matrícula no encontrada");

$d = $result->fetch_assoc();

$fecha_formateada = date("d/m/Y", strtotime($d['fecha_registro']));
$fecha_nacimiento_formateada = date("d/m/Y", strtotime($d['fecha_nacimiento']));

function calcularEdad($fecha_nacimiento) {
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac);
    return $edad->y; // devuelve solo los años
}

$edad = calcularEdad($d["fecha_nacimiento"]);

// ==========================
//   HTML del PDF
// ==========================
$html = "
<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    padding: 0; 
    display: flex;
    flex-direction: column;
    font-size: 14px;
}
.box {
}
.box1 {
    display: flex;
    background: red;
    height: 100px;
}
.row {
    display: flex;
    gap: 1rem;
}
    
h2 { text-align: center; }
.col {
    float: left;
    width: 48%;
}
.clear { clear: both; }

td {
    width: 100%;
}
</style>

<div style='width: 100px; height: 125px; border: 1px solid black; position: absolute; right: 0; top: 0;'></div>

<table width='100%' border='0' cellspacing='0' cellpadding='5'>
    <tr>
        <td>
            <div class='row' style='font-size: 9px; width: 125px;'>
                <p>
                    CENTRO DE ESTUDIOS SAN IGNACIO DE VILLA ALEMANA
                </p>
            </div>
        </td>
    </tr>
</table>

<img height='100px' style='position: absolute; left: 50%; transform: translateX(-50%);' src='https://sanignaciova.cl/assets/icons/logo-2.svg'>

<div class='box' style='margin-top: 7rem'>
    <h2>FICHA DE MATRÍCULA 2026</h2>
</div>

<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td>
           <b>FECHA:</b> 
          
        </td>
        <td>
            $fecha_nacimiento_formateada
        </td>
        <td>
           <b>CURSO:</b> 
        </td>
        <td>
           ".htmlspecialchars($d["nivel"] . " Nivel " . $d["letra"] ?: "Sin información")."
        </td>
        <td>
           <b>N° MAT.</b> 
        </td>
        <td>
        ".htmlspecialchars($d["id"])."
        </td>
    </tr>
</table>

<h2>DATOS DEL ALUMNO</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td colspan='3'>
           <b>NOMBRE</b><br>
           ".htmlspecialchars($d["nombre_estudiante"]." ".$d["apellidos_estudiante"])."
        </td>
        <td colspan='3'>
            <b>CORREO ELECTRÓNICO</b><br>
            ".htmlspecialchars($d["correo_estudiante"] ?: "Sin información")."
        </td>
        
    </tr>
    <tr>
        <td colspan='3'>
            <b>FECHA DE NACIMIENTO</b><br>
            $fecha_nacimiento_formateada
        </td>
        <td colspan='3'>
            <b>RUT</b><br>
            ".htmlspecialchars($d["rut_estudiante"] ?: "Sin información")."
        </td>
    </tr>
    <tr>
        <td colspan='3'>
            <b>EDAD</b><br>
            ".$edad. ' años' ."
        </td>
        <td colspan='3'>
            <b>TELÉFONO/CELULAR</b><br>
            ".htmlspecialchars($d["telefono_estudiante"] ?: "Sin información")."
        </td>
    </tr>
    <tr>
        <td colspan='6'>
            <b>DIRECCIÓN/DOMICILIO</b><br>
            ".htmlspecialchars($d["direccion_estudiante"] ?: "Sin información")."
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <b>PROGRAMA SOCIAL</b><br>
            ".htmlspecialchars($d["programa_estudiante"] ?: "Sin información")."
        </td>
        <td colspan='2'>
            <b>ETNIA</b><br>
            ".htmlspecialchars($d["etnia_estudiante"] ?: "Sin información")."
        </td>
        <td colspan='2'>
            <b>HIJOS</b><br>
           ".htmlspecialchars($d["hijos_estudiante"] ?: "Sin información")."
        </td>
    </tr>
</table>

<h2>DATOS DEL APODERADO</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td colspan='2'>
           <b>NOMBRE</b><br>
            ".htmlspecialchars($d["nombre_apoderado"] ?: "Sin información")."
        </td>     
    </tr>
    <tr>
        <td>
           <b>PARENTESCO</b><br>
            ".htmlspecialchars($d["parentezco_apoderado"] ?: "Sin información")."
        </td>  
        <td>
           <b>RUT</b><br>
            ".htmlspecialchars($d["rut_apoderado"] ?: "Sin información")."
        </td>    
    </tr>
    <tr>
        <td>
           <b>SITUACIÓN ESPECIAL</b><br>
            ".htmlspecialchars($d["situacion_especial_apoderado"] ?: "Sin información")."
        </td>  
        <td>
           <b>TELÉFONO/CELULAR</b><br>
            ".htmlspecialchars($d["telefono_apoderado"] ?: "Sin información")."
        </td>    
    </tr>
    <tr>
        <td colspan='2'>
           <b>DIRECCIÓN/DOMICILIO</b><br>
            ".htmlspecialchars($d["direccion_apoderado"] ?: "Sin información")."
        </td>      
    </tr>
</table>

<h3>COMPROMISO DE RESPONSABILIDAD</h3>
<p style='text-transform: uppercase; t'>
    Mediante el presente documento, acepto todas las condiciones que han permitido la matrícula
    como también el mantenerme preocupado e informado por el estado académico y conductual del
    alumno, respetando las normas internas señaladas en el reglamento de convivencia escolar y
    acatando sus respectivas sanciones
    (Se accede al reglamento de convivencia en <u style='text-transform: lowercase'>sanignaciova.cl/reglamentos.html</u>, el cual se compromete a cumplir,
    según disposición del establecimiento).
</p>

<div style='text-align: center; margin-top: 2rem'>______________________________<br>FIRMA APODERADO</div>
";

// ==========================
//  Generar el PDF
// ==========================
$dompdf = new Dompdf([
    "isRemoteEnabled" => true
]);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("FichaMatricula_$id.pdf", ["Attachment" => false]);
exit;
?>
