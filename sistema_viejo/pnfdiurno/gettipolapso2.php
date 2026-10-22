<?php
include('db.php');  

$sql = "SELECT DISTINCT tiplap FROM tipos_lapso";  
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {

	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["tiplap"].'">'.$fila["tiplap"].'</option>';

	}

}

?>