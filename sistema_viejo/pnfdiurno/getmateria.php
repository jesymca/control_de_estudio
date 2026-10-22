<?php

$pensum=$_POST["pensum"];
include('/Classes/class_api.php');
$pdf=new PDF();
$pdf->getmateria($pensum);

?>