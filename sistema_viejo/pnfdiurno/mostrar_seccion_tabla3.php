<?php
$pensum=$_POST['pensum'];
$cedula=$_POST['cedula'];
$carrera=$_POST['carrera']; 
 
include('/Classes/class_api.php');
$con=new PDF();

$con->tabla_contenido_notas2($pensum,$cedula,$carrera);

?>
