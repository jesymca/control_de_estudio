<?php


include('db.php');  


// $sql = "TRUNCATE TABLE `agregarseccion`"; 
// $resultado = $conn->query($sql);



$archivo = fopen("C:".chr(47)."TEMP".chr(47)."TODAS_LAS_SECCION_ACTUALES.SQL","w");
                                                                                                                                                                                                           
fwrite($archivo,"INSERT INTO ".chr(96)."agregarseccion".chr(96)." (".chr(96)."pensum".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."cod_doc".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."electiva".chr(96).") VALUES". PHP_EOL);     

include "db.php";

$sql = "SELECT DISTINCT SUBSTRING(`cod_mat`,1,1) AS pensum, `cod_mat`,`seccion`,`cod_doc`,`lapso`,`electiva` FROM `notas`";
$resultado = $conn->query($sql);
$contador=1;
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {   

		$pensum=substr($fila['cod_mat'], 0, 1)."XC";
		$cod_mat = $fila['cod_mat'];
		$seccion = $fila['seccion'];
		$cod_doc = $fila['cod_doc'];
		$lapso = $fila['lapso'];		
		$electiva = $fila['electiva'];


if($contador<>$resultado->num_rows){
		fwrite($archivo,"('".$pensum."','".$cod_mat."','".$seccion."','".$cod_doc."','".$lapso."','".$electiva."'),". PHP_EOL);                    
}else{
	   fwrite($archivo,"('".$pensum."','".$cod_mat."','".$seccion."','".$cod_doc."','".$lapso."','".$electiva."');". PHP_EOL);                    
}
$contador++;
	}  
}	

fclose($archivo);
$contador=$contador-1;
echo "Se generaron : ".$contador." secciones   C:TEMP ".chr(92)."TODAS_LAS_SECCION_ACTUALES.SQL";



?>