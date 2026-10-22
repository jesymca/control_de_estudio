<?php
include('db.php');


$cod_mat=$_POST["cod_mat"];
$pensum=substr($cod_mat, 0, 1)."XC";


$sql = "SELECT electivas.cod_ele,lismat.electiva,agregarseccion.cod_mat,lismat.cod_mat FROM agregarseccion,lismat,electivas where agregarseccion.cod_mat='".$cod_mat."' and lismat.electiva='1' and agregarseccion.cod_mat=lismat.cod_mat and electivas.cod_ele=agregarseccion.electiva";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
		echo $fila["cod_ele"];
	}

}



?>