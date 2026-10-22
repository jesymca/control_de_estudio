<?php
$pensum=$_POST['pensum'];
$cedula=$_POST['cedula'];
$lapso=$_POST['lapso']; 
 
include('/Classes/class_api.php');
$con=new PDF();

$con->tabla_contenido_notas($pensum,$cedula,$lapso);

?>
