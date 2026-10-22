<?php
include('db.php');
$lapso=$_POST["lapso"];
$cod_mat=$_POST["cod_mat"];
$pensum=$_POST["pensum"];



$sql = "SELECT * FROM agregarseccion where cod_mat='".$cod_mat."' and lapso='".$lapso."'"; 
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
	     $electiva=$fila["electiva"];
	}

}
// $lapso="2014-3";
// $cod_mat="M1BNC";
// $electiva="AB";


$sql = "SELECT * FROM electivas where pensum='".$pensum."' and cod_ele='".$electiva."' ORDER BY `id` ASC";

$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["cod_ele"].'">'.$fila["descrip2"].'</option>';
	}

}



?>