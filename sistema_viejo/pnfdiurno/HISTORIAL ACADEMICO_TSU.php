<?php

include('/Classes/class_api.php');
$pdf=new PDF();

$cedula=$_POST["cedula"];
$pensum=$_POST["pensum"];
$grado=$_POST["grado"];


$pdf->encabezado_historial($pensum,$cedula); 
$pdf->contenido_historial($pensum,$grado,$cedula);

$pdf->pie_de_pagina_historial($X1B,"Portar Web",$ira,$APROBADOS,$FALTANTES,$MAX_A_CURSAR,$nota_sum,$cedula,$pensum,$grado);


$pdf->Output();

?>
