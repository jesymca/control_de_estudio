<?php
include('db.php');

$carrera=$_POST["carrera"];


$sql = "SELECT DISTINCT lapso FROM notas where carrera='".$carrera."' ORDER BY lapso DESC"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
		
	}

}

?>