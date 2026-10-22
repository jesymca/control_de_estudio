<?php

include('/Classes/class_api.php');
$pdf=new PDF();

$cod_doc="303";
$cod_mat="I0AAC";
$lapso="2017-2";
$seccion="70";

echo $pdf->getcantidad($cod_doc,$cod_mat,$lapso,$seccion);

