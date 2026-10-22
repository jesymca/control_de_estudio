<?php

$hora=$_POST['hora'];
$cantidad=$_POST['cantidad'];

include('/Classes/class_api.php');
$pdf=new PDF();

$hora_final=$pdf->calcular_hora($hora,$cantidad);


echo Trim($hora_final);

?>