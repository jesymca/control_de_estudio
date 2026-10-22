<?php
include('db.php');

$carrera=substr($_POST["pensum"], 0, 1);
                
$sql = "SELECT DISTINCT SUBSTRING(lapso,1,4) as lapso FROM `lapso` ORDER BY lapso DESC"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 

		echo '<option value="'.substr($fila["lapso"], 0, 4).'">'.substr($fila["lapso"], 0, 4).'</option>';
		
	}

}

?>