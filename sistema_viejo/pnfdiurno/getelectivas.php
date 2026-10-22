<?php

include('db.php');

$pensum=$_POST["pensum"];
$cod_mat=$_POST["cod_mat"];

$sql = "SELECT * FROM lismat where pensum='".$pensum."' and cod_mat='".$cod_mat."' and electiva='1' ORDER BY `id` ASC"; 
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
	     $dat=$fila["electiva"];
	}

}
if($dat=="1"){

$sql = "SELECT * FROM electivas where pensum='".$pensum."' ORDER BY `id` ASC"; 

$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["cod_ele"].'">'.$fila["descrip2"].'</option>';
	}

}

}

?>