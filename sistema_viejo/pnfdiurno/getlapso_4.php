<?php
include('db.php');

$cod_doc=$_POST["cod_doc"];
$cod_mat=$_POST["cod_mat"];


$sql = "SELECT DISTINCT lapso FROM `agregarseccion` where cod_doc='".$cod_doc."' and cod_mat='".$cod_mat."'"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
		
	}

}

?>