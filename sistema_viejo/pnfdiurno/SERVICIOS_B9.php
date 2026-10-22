<?php

$cedula = $_POST['cedula'];

 include "db.php";

$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 		
		$id=$fila['id'];		
	}
}
$id="?id=".$id;                              
header("Location: CONSTANCIA2.php".$id);

?>