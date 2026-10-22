<?php
include('/Classes/class_api.php');
$x=new PDF();

$cedula=$_POST['cedula'];
include('db.php');   
                                                                                                                                                                                                                                                                                                                                                                                                         
$sql = "SELECT DISTINCT carrera FROM notas WHERE codigo='".$cedula."' ORDER BY carrera DESC"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {       
	while($fila = $resultado->fetch_assoc()) {		
	   $x->pensum_combo($fila["carrera"]);
	}
}
?>