<?php
include('db.php');  

$carrera=substr($_POST["pensum"], 0, 1);

$sql = "SELECT DISTINCT cedula,nombre,carrera FROM alumno where carrera='".$carrera."' ORDER BY nombre";
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["cedula"].'">'.strtoupper($fila["nombre"]).' | '.$fila["cedula"].'</option>';
	}
}        
?>