<?php
include('db.php');

$sql = "SELECT DISTINCT lapso FROM lapso ORDER BY lapso DESC"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
		
	}

}

?>