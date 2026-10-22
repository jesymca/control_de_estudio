<?php
$pensum=$_POST['pensum'];
$cedula=$_POST['cedula'];
$cod_mat=$_POST['cod_mat']; 
include('/Classes/class_api.php');
$con=new PDF();
$con->tabla_contenido_notas3($pensum,$cedula,$cod_mat);

?>
