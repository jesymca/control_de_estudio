<?php

function carrera_corta($cadena){

	include('db.php');  

	$pensum=$cadena."XC";

	$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
	$resultado = $conn->query($sql); 

	if ($resultado->num_rows > 0) {		
		while($fila = $resultado->fetch_assoc()) { 
			$descripcion2=$fila["descripcion2"];
		}
	}     
	// $conn->close();
	return $descripcion2;
}

function carrera_larga($cadena){

	include('db.php');  

	$pensum=substr($cadena, 0, 1)."XC";

	$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
	$resultado = $conn->query($sql); 


	if ($resultado->num_rows > 0) {		
		while($fila = $resultado->fetch_assoc()) { 
			$descripcion=$fila["descripcion"];
		}
	}  
	// $conn->close();
	return $descripcion;
}

function pensum($cadena){

	include('db.php');  

	$pensum=substr($cadena, 0, 1)."XC";

	$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
	$resultado = $conn->query($sql); 


	if ($resultado->num_rows > 0) {		
		while($fila = $resultado->fetch_assoc()) { 
			$pensum=$fila["pensum"];
		}
	}  
	// $conn->close();
	return $pensum;
}