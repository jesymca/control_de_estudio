<?php
include('db.php');

$cedula=$_POST["cedula"];



$sql = "SELECT DISTINCT lapso FROM `notas` where codigo='".$cedula."' ORDER BY lapso DESC"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
		
	}

}

?>