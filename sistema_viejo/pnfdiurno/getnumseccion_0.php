<?php
include('db.php');

$carrera=$_POST["carrera"];

$sql = "SELECT DISTINCT seccion FROM notas  where carrera='".$carrera."' ORDER BY `seccion`";
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		if ($fila["seccion"]<>""){
          echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';

		}
		
	}

}

?>