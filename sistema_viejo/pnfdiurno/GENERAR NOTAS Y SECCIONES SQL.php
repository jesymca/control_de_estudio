<?php


// // include "db.php";
// $servidor = "localhost";
// $usuario = "root";
// $clave = "SR19021601***";
// $base_datos = "automotriz";

// // require('configuracion.php');
// $conn = new mysqli($servidor, $usuario, $clave, $base_datos);

// $pensum=$_POST["pensum"];
// $pensum="AXC";

// echo "SE HAN GENERADO LOS ARCHIVOS EN C:".chr(47)."TEMP".chr(47)."<br>";
// echo "Pensum:".$pensum."<br>";

// $sql = "SELECT DISTINCT `cod_mat` FROM `notas` WHERE carrera='".substr($pensum,0,1)."'";
// $resultado = $conn->query($sql);
// $x1=1;

// if ($resultado->num_rows > 0) {
// 	while($fila = $resultado->fetch_assoc()) {  
// 	      materias($fila['cod_mat']);
// 	      echo $x1.") ".$fila['cod_mat']."_NOTAS.SQL"."<br>";
// 	      $mat=$resultado->num_rows;
//           $x1++;
// 	}  
// }	

// echo ""."<br>";
// echo "****************"."<br>";

// $sql = " SELECT cod_mat FROM agregarseccion where pensum='".$pensum."' GROUP BY cod_mat HAVING COUNT(*) > 1";
// $resultado = $conn->query($sql);
// if ($resultado->num_rows > 0) {
// 	while($fila = $resultado->fetch_assoc()) { 
// 	      seccion($fila['cod_mat'],$pensum);
// 	      echo "seccion: C:".chr(47)."TEMP".chr(47).$fila['cod_mat']."_SECCION.SQL"."<br>";
// 	      $sec=$resultado->num_rows;
// 	}  
// }

materias("M2BB","R1ARC");
materias("M2CB","R1ASC");
materias("M2FB","R1AWC");
materias("M1AB","R1AGC");
materias("M1BB","R1AHC");
materias("M1CB","R1AIC");
materias("M1DB","R1AJC");
materias("M1EB","R1AKC");
materias("M1FB","R1ALC");
materias("M1GB","R1AMC");
materias("M1HB","R1AOC");
materias("M1IB","R1APC");
materias("M2AB","R1AQC");





function materias($MATERIA,$cod_mat)
{

$archivo = fopen("C:".chr(47)."TEMP".chr(47).$MATERIA."_NOTAS.SQL","w");
fwrite($archivo,"INSERT INTO ".chr(96)."notas".chr(96)." (".chr(96)."codigo".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."carrera".chr(96).", ".chr(96)."nota".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."tiplap".chr(96).", ".chr(96)."cod_doc".chr(96).", ".chr(96)."cod_usu".chr(96).", ".chr(96)."acu".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."electiva".chr(96).", ".chr(96)."fecha".chr(96).") VALUES". PHP_EOL);     

// include "db.php";
$servidor = "localhost";
$usuario = "root";
$clave = "SR19021601***";
$base_datos = "termica";

// require('configuracion.php');
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);


// include "db.php";
$sql = "SELECT * FROM notas WHERE cod_mat='".$MATERIA."'";
$resultado = $conn->query($sql);
$contador=1;
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {                      
		$codigo = $fila['codigo'];
		$cod_mat = $cod_mat;
		$carrera = "R";
		$nota = $fila['nota'];	
		$lapso	= $fila['lapso'];
		$tiplap	= $fila['tiplap'];
		$cod_doc = $fila['cod_doc'];
		$cod_usu = "S-V";
		$acu = $fila['acu'];	
		$seccion = $fila['seccion'];
        $electiva = "0";
        $fecha = "12-03-2020";  
		
if($contador<>$resultado->num_rows){
	   fwrite($archivo,"('".$codigo."','".$cod_mat."','".$carrera."','".$nota."','".$lapso."','".$tiplap."',".$cod_doc.",'".$cod_usu."',".$acu.",'".$seccion."','".$electiva."','".$fecha."'),". PHP_EOL);                    
}else{
	   fwrite($archivo,"('".$codigo."','".$cod_mat."','".$carrera."','".$nota."','".$lapso."','".$tiplap."',".$cod_doc.",'".$cod_usu."',".$acu.",'".$seccion."','".$electiva."','".$fecha."');". PHP_EOL);                    
}
$contador++;
	}  
}	

fclose($archivo);


echo "Se genero ".$cod_mat."<br>";
} 



// function seccion($MATERIA,$PENSUM)
// {
// $archivo = fopen("C:".chr(47)."TEMP".chr(47).$MATERIA."_SECCION.SQL","w");

// fwrite($archivo,"INSERT INTO ".chr(96)."agregarseccion".chr(96)." (".chr(96)."pensum".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."cod_doc".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."aula".chr(96).", ".chr(96)."descrip".chr(96).", ".chr(96)."hora".chr(96).", ".chr(96)."tipo".chr(96).", ".chr(96)."electiva".chr(96).") VALUES". PHP_EOL);     

// include "db.php";

// $sql = "SELECT * FROM agregarseccion WHERE cod_mat='".$MATERIA."' and pensum='".$PENSUM."'";
// $resultado = $conn->query($sql);
// $contador=1;
// if ($resultado->num_rows > 0) {
// 	while($fila = $resultado->fetch_assoc()) {   

// 		$pensum = $fila['pensum'];
// 		$cod_mat = $fila['cod_mat'];
// 		$seccion = $fila['seccion'];
// 		$cod_doc = $fila['cod_doc'];
// 		$lapso	= $fila['lapso'];
// 		$aula = $fila['aula'];	
// 		$descrip = utf8_decode($fila['descrip']);
// 		$hora = $fila['hora'];
// 		$tipo = $fila['tipo'];
//         $electiva = $fila['electiva'];

// if($contador<>$resultado->num_rows){
// 		fwrite($archivo,"('".$pensum."','".$cod_mat."','".$seccion."','".$cod_doc."','".$lapso."','".$aula."','".$descrip."','".$hora."','".$tipo."','".$electiva."'),". PHP_EOL);                    
// }else{
// 	   fwrite($archivo,"('".$pensum."','".$cod_mat."','".$seccion."','".$cod_doc."','".$lapso."','".$aula."','".$descrip."','".$hora."','".$tipo."','".$electiva."');". PHP_EOL);                    
// }
// $contador++;
// 	}  
// }	

// fclose($archivo);
// } 




?>