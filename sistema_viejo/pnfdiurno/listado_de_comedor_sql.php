<?php

listado_a_comedor("comedor","2020-1");

function listado_a_comedor($tabla,$lapso)
{
	$archivo = fopen("D:".chr(47)."DESCARGA".chr(47)."listado a comedor.txt","w");
	
	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."CEDULA".chr(96).", ".chr(96)."NOMBRE".chr(96).", ".chr(96)."CARRERA".chr(96).") VALUES". PHP_EOL);
	include "db.php";
  
	$sql = "SELECT DISTINCT SUBSTRING(alumno.cedula,2,9) as CEDULA,alumno.nombre as NOMBRE,pensum.descripcion2 as CARRERA FROM `alumno`,`pensum`,`notas` WHERE alumno.carrera=SUBSTRING(pensum.pensum,1,1) and notas.codigo=alumno.cedula and notas.lapso='".$lapso."' ORDER BY alumno.nombre ASC";
	$resultado = $conn->query($sql);

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$cedula = $fila['CEDULA'];
			$nombre = $fila['NOMBRE'];
			$carrera = $fila['CARRERA'];			
			fwrite($archivo,"('".$cedula."', '".$nombre."', '".$carrera."');". PHP_EOL);
		}
	}

	fclose($archivo);

    $archivo2="D:".chr(47)."DESCARGA".chr(47)."listado a comedor.txt";

   if( file_exists($archivo2) ) 
	{       
        header("Content-type: application/txt");        
        header("Content-Disposition: attachment; filename=".$archivo2);
        header("Content-length: ".filesize($archivo2));    
		readfile($archivo);
	}

}