<?php


$cod_mat=$_POST["materia"];
$cedula=$_POST["cedula"];

include('/Classes/class_api.php');
$pdf=new PDF();

$pdf->getmateria_no_vista($cod_mat, $cedula);

?>