<?php
include('db.php');

$sql = "SELECT * FROM estados ORDER BY `estado` ASC"; 

$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["id_estado"]."|".$fila["estado"].'">'.$fila["estado"].'</option>';
		
	}

}

?>