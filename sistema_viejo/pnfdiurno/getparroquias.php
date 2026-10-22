<?php
include('db.php');

$municipio=$_POST["municipio"];



$sql = "SELECT * FROM parroquias where id_municipio='".$municipio."' ORDER BY `parroquia` ASC"; 

$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["parroquia"].'">'.$fila["parroquia"].'</option>';
	}
}

?>