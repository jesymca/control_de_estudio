<?php
$cedula=$_POST["cedula"];
$carrera=$_POST["carrera"];

include('/Classes/class_api.php');
$con=new PDF();
$con->combo_materias($cedula,$carrera);

?>