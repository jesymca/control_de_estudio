<?php
include('db.php');

$cedula=$_POST["cedula"];

$sql = "SELECT DISTINCT SUBSTRING(notas.cod_mat,3,2) AS cod_mat,lismat.descrip2,lismat.trayecto FROM `notas`,lismat where notas.codigo='".$cedula."' and notas.cod_mat=lismat.cod_mat ORDER BY SUBSTRING(`cod_mat`,3,2),lismat.trayecto ASC";
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {	
	echo '<option value="">Seleccionar</option>';	
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"]." - ".$fila["trayecto"]." - ".$fila["descrip2"].'</option>';
	}
}

?>