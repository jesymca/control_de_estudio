<?php
include 'menu.php';
$lapso=$_POST["lapso"];
$pathname="C:temp";

if (is_dir($pathname) || empty($pathname)) {
        
   }else{
   	  mkdir($pathname);   	  
   }


   date_default_timezone_set('America/Caracas');
   $fechaActual = date('d-m-Y');  

$pathname2=$pathname.chr(47).$fechaActual;

if (is_dir($pathname2) || empty($pathname2)) {
        
}else{
   mkdir($pathname2);   	  
}

tabla_comedor($lapso,"comedor",$fechaActual,$pathname);

function tabla_comedor($lapso,$tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT substr(`alumno`.`cedula`,2,9) AS `CEDULA`,`alumno`.`nombre` AS `NOMBRE`,`pensum`.`descripcion2` AS `CARRERA` from ((`alumno` join `pensum`) join `notas`) where ((`alumno`.`carrera` = substr(`pensum`.`pensum`,1,1)) and (`notas`.`codigo` = `alumno`.`cedula`) and (`notas`.`lapso` = '".$lapso."'))";
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$cedula = $fila['CEDULA'];
			$nombre = $fila['NOMBRE'];
			$carrera = $fila['CARRERA'];			
			$cant=$resultado->num_rows;
			fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."CEDULA".chr(96).", ".chr(96)."NOMBRE".chr(96).", ".chr(96)."CARRERA".chr(96).") VALUES ('".$cedula."', '".$nombre."', '".$carrera."');". PHP_EOL);
			$contador++;
		}
	}
	fclose($archivo);
	include('/Classes/class_api.php');
    $x=new PDF();
    $mensaje=$pathname.chr(92).$fechaActual.chr(92).$tabla.".sql";

    $x->mensaje_color("principal.php","principal.php",$mensaje,0,0);

	}

?>