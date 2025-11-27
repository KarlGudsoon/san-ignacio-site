<?php

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$rut = $_POST['rut'] ?? '';
$curso = $_POST['curso'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';

$fecha_actual = date("d/m/Y");

$html = "
<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    padding: 0; 
    display: flex;
    flex-direction: column;
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
            $fecha_actual
        </td>
        <td>
           <b>CURSO:</b> 
        </td>
        <td>
            $curso
        </td>
        <td>
           <b>N° MAT.</b> 
        </td>
        <td>
        </td>
    </tr>
</table>

<h2>DATOS DEL ALUMNO</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td colspan='3'>
           <b>NOMBRE</b><br>
           $nombre 
        </td>
        <td colspan='3'>
            <b>CORREO ELECTRÓNICO</b><br>
            $fecha_actual
        </td>
        
    </tr>
    <tr>
        <td colspan='3'>
            <b>FECHA DE NACIMIENTO</b><br>
            $nombre 
        </td>
        <td colspan='3'>
            <b>RUT</b><br>
            $rut
        </td>
    </tr>
    <tr>
        <td colspan='3'>
            <b>EDAD</b><br>
        
        </td>
        <td colspan='3'>
            <b>TELÉFONO/CELULAR</b><br>
        </td>
    </tr>
    <tr>
        <td colspan='6'>
            <b>DIRECCIÓN/DOMICILIO</b><br>
            $nombre 
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <b>PROGRAMA SOCIAL</b><br>
            
        </td>
        <td colspan='2'>
            <b>ETNIA</b><br>
            
        </td>
        <td colspan='2'>
            <b>HIJOS</b><br>
           
        </td>
    </tr>
</table>

<h2>DATOS DEL APODERADO</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td colspan='2'>
           <b>NOMBRE</b><br>

        </td>     
    </tr>
    <tr>
        <td>
           <b>PARENTESCO</b><br>

        </td>  
        <td>
           <b>RUT</b><br>

        </td>    
    </tr>
    <tr>
        <td>
           <b>SITUACIÓN ESPECIAL</b><br>

        </td>  
        <td>
           <b>TELÉFONO/CELULAR</b><br>

        </td>    
    </tr>
    <tr>
        <td colspan='2'>
           <b>DIRECCIÓN/DOMICILIO</b><br>

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

<div style='text-align: center;'>______________________________<br>FIRMA APODERADO</div>

<div class='box' style='margin-top: 2rem'>
    <h2>Ficha del Estudiante</h2>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Apellido:</strong> $apellido</p>
    <p><strong>RUT:</strong> $rut</p>
    <p><strong>Curso:</strong> $curso</p>
    <p><strong>Dirección:</strong> $direccion</p>
    <p><strong>Teléfono:</strong> $telefono</p>
</div>
";

// Opciones del PDF
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();

// Mostrar sin descargar
$dompdf->stream("ficha_$nombre.pdf", ["Attachment" => false]);
exit;
