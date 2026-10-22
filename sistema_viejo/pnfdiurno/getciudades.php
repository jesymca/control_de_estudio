<?php

include('db.php');

$estado=$_POST["estado"];


$sql = "SELECT * FROM ciudades where id_estado='".$_POST["estado"]."' ORDER BY `ciudad` ASC"; 

$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["id_estado"]."|".$fila["ciudad"].'">'.$fila["ciudad"].'</option>';
		
	}

}

?>