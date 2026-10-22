<?php
include('db.php');


$estado=$_POST["estado"];


$sql = "SELECT * FROM municipios where id_estado='".$estado."' ORDER BY `municipio` ASC"; 

$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	echo '<option value="">Seleccionar</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["id_municipio"]."|".$fila["municipio"].'">'.$fila["municipio"].'</option>';
	}
}

?>