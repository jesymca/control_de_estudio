<?php

$cedula=$_POST['cedula'];
$carrera=$_POST['carrera'];

include('db.php'); 

if ($carrera<>"") {
	$sql = "SELECT DISTINCT lapso FROM notas where codigo='".$cedula."' and carrera='".$carrera."' ORDER BY lapso DESC"; 
}else{
    $sql = "SELECT DISTINCT lapso FROM notas where codigo='".$cedula."' ORDER BY lapso DESC"; 
}

$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	    echo '<option value="">Todos</option>';
	while($fila = $resultado->fetch_assoc()) { 
		echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
	}
}

?>