<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
} else {
	header("Location: index.html");
	exit;
}
$now = time();
if($now > $_SESSION['expire']) {
	session_destroy();
	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
	exit;
}
include "db.php";

date_default_timezone_set('America/Caracas');
$DIA=date("d");
$MES=date("m");
$AÑO=date("Y");

$hoy = date("d-m-Y");  	

$sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
$resultado = $conn->query($sql);

$CREDSUM=0;

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
		$NOMBRE_USER=$fila['nombre'];              
	}
}


function calcular_resumida($pensum,$X1B,$lismat_cod_mat,$cedula,$X1B,$descrip2,$semestre,$creditos,$trayecto,$aprobatori,$divicion,$electiva)
{ 			

	global $nota_r2,$X1B,$X0,$esta,$FALTANTES,$APROBADOS;	
	include "db.php";

	$sql = "SELECT `id`,`codigo`, `cod_mat`, `carrera`, `nota`, `lapso`, `tiplap`, `cod_doc`, `cod_usu`, `acu`, `seccion`, `electiva`, `fecha` FROM `notas` WHERE `codigo`='".$cedula."'";			
	$result = $conn->query($sql);
	$i=0;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id_a[$i]=$row['id'];	
			$codigo_a[$i]=$row['codigo'];
			$cod_mat_a[$i]=$row['cod_mat'];
			$carrera_a[$i]=$row['carrera'];
			$nota_a[$i]=$row['nota'];
			$lapso_a[$i]=$row['lapso'];
			$tiplap_a[$i]=$row['tiplap'];
			$cod_doc_a[$i]=$row['cod_doc'];
			$cod_usu_a[$i]=$row['cod_usu'];
			$acu_a[$i]=$row['acu'];
			$seccion_a[$i]=$row['seccion'];
			$electiva_a[$i]=$row['electiva'];
			$fecha_a[$i]=$row['fecha'];
			$i++;
			
		} 
	}

	if ($lapso_a<>"") {
		$max_lapso=max($lapso_a);
	}	

	$materia=substr($lismat_cod_mat, 2, 2);
	$mate=substr($lismat_cod_mat, 1, 1);
	$carrera=substr($lismat_cod_mat, 0, 1);
	$plan=substr($lismat_cod_mat, 4, 1);

	$materiaT=$carrera."T".$materia.$plan;
	$materiaE=$carrera."E".$materia.$plan;

	for ($i=0; $i <count($cod_mat_a); $i++) {
		if(substr($cod_mat_a[$i], 2, 2)==$materia){ 
			$temp_lapso[$i]=$lapso_a[$i];
		}
	}
	if ($temp_lapso<>"") {
		$max_lapso=max($temp_lapso);
	}
	
	for ($i=0; $i <count($cod_mat_a); $i++) { 
		if($cod_mat_a[$i]=="$materiaT" AND $max_lapso==$lapso_a[$i]){				
			$notaT="true";		
			$nota_r2=$nota_a[$i];	
			$lapso=$lapso_a[$i]."-".$max_lapso;
			$lapso_T=$lapso_a[$i];							
			$esta="SI"; 	
		}

		if($cod_mat_a[$i]=="$materiaE" AND $max_lapso==$lapso_a[$i]){					
			$notaE="true";	
			$nota_r2=$nota_a[$i];	
			$lapso=$lapso_a[$i]."-".$max_lapso;
			$lapso_E=$lapso_a[$i];				
			$esta="SI"; 	
		}

		if(substr($cod_mat_a[$i], 2, 2)==substr($lismat_cod_mat, 2, 2)){
			$numero_veses=$numero_veses+1;		
		}
	}
	if($notaT<>"true"){
		$notaT="false";
	}
	if($notaE<>"true"){
		$notaE="false";
	}
	if($notaT=="false" AND $notaE=="false"){

		$materia0=$carrera."0".$materia.$plan;
		$materia1=$carrera."1".$materia.$plan;
		$materia2=$carrera."2".$materia.$plan;
		$materia3=$carrera."3".$materia.$plan;

		for ($i=0; $i <count($cod_mat_a); $i++) { 
			if($cod_mat_a[$i]=="$materia0"){							
				$nota0=$nota_a[$i];		
				$lapso=$lapso_a[$i];				
			}
			if($cod_mat_a[$i]=="$materia1"){								
				$nota1=$nota_a[$i];		
				$lapso=$lapso_a[$i];
				$cod_ele=$electiva_a[$i];				
			}
			if($cod_mat_a[$i]=="$materia2"){							
				$nota2=$nota_a[$i];		
				$lapso=$lapso_a[$i];				
			}
			if($cod_mat_a[$i]=="$materia3"){								
				$nota3=$nota_a[$i];		
				$lapso=$lapso_a[$i];				
			}
			
		}

		if($trayecto==0){		   
			$nota_r2=$nota0;					   
		}else{
			if($divicion==1){ 
				$nota_r2=$nota1;				
			}
			if($divicion==2){
				$nota_r2=($nota1+$nota2)/2;				
			}
			if($divicion==3){ 
				$nota_r2=($nota1+$nota2+$nota3)/3;				
			}
		}     
		
	}  

	if ($nota_r2<>"") { 
		$materiaP=$carrera."P".$materia.$plan;
		for ($i=0; $i <count($cod_mat_a); $i++) { 
			if($cod_mat_a[$i]=="$materiaP"){
				if ($nota_r2>5 and $nota_r2<$aprobatori and $aprobatori<>16) {
					$per="true";
					$nota_p=$nota_a[$i];		
					$lapso=$lapso_a[$i];
					$t30=($nota_r2*100)/20;
					$t70=100-$t30;
					$t70_2=$nota_p*($t70/100);
					$nota_r2=$nota_r2+$t70_2;
					$nota_r2=round($nota_r2, 0, PHP_ROUND_HALF_UP);
				}				
			}				
		}

            ///////////// VESES ///////////////
		if($divicion==$numero_veses){
			$numero_veses=1;
		}
		if($numero_veses>$divicion){
			$numero_veses=$numero_veses-$divicion;
		}			
            ///////////// VESES ///////////////
		
		if($nota_r2<$aprobatori){				
			$FALTANTES=$FALTANTES+1;
		}else{				
			$APROBADOS=$APROBADOS+1;
		}
		return $nota_r2;
	}
}

function cantidad_materias($pensum,$grado)
{

	include "db.php";

	$sql = "SELECT * FROM `lismat` WHERE `pensum` LIKE '".$pensum."' AND `cod_mat` LIKE '%IR%' AND grado='".$grado."'"; 
	$resultado = $conn->query($sql); 
	if ($resultado->num_rows > 0) {
		$dat=$resultado->num_rows;
	}

	return $dat; 

}	

function materias_aprobadas($pensum,$cedula,$grado)
{ 

	include "db.php";
	$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND grado='".$grado."' GROUP BY id ASC";
	$resultado = $conn->query($sql);

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 

			$lismat_cod_mat2[$x1]=$fila['cod_mat'];			
			$descrip2[$x1]=$fila['descrip2'];
			$creditos[$x1]=$fila['creditos'];
			$semestre[$x1]=$fila['semestre'];			
			$nota_cat[$x1]=$fila['nota'];
			$divicion[$x1]=$fila['divicion'];
			$trayecto[$x1]=$fila['trayecto'];
			$aprobatori[$x1]=$fila['aprobatori'];
			$electiva[$x1]=$fila['electiva'];

			if(substr($lismat_cod_mat2[$x1], 1, 1)=="R"){	

				$notas_res=calcular_resumida($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1],$electiva[$x1]);
				if($notas_res>=$aprobatori[$x1]){
					$dat++;
				}
			}

			$x1=$x1+1;
		}
	}

	return $dat;  
}

function actualizar_ira($cedula,$ira)
{
	include "db.php";
	$sql = "UPDATE `alumno` SET ira='".$ira."' WHERE cedula='".$cedula."'";
	$result = $conn->query($sql);
}	


function comprobar_tsu($cedula,$grado)
{
	include "db.php";
	$query = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_array($result))
	{
		$carrera=$row["carrera"];
	}


	include('getpensum3_clase.php');
	$con = new carreras();
	$pensum= $con->pensum($carrera);      


	$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
	$resultado = $conn->query($sql);

	if (!$resultado) {	
		exit;
	}
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 

			$CODIGO=$fila['codigo'];  
			$cedula=$fila['cedula']; 	    
			$CARRERA=$fila['carrera'];
			$MENCION=$fila['mencion'];
			$PLAN=$fila['plan'];
			$NOMBRE=$fila['nombre'];
			$SEMESTRE=$fila['semestre'];
			$ACTIVIDAD=$fila['actividad'];
			$NIVEL=$fila['nivel'];
		}
	}


	$x1=0;
	$APROBADOS=0;
	$FALTANTES=0;

	if($grado=="T"){

		$cantidad_materias=cantidad_materias($pensum,$grado);
		$materias_aprobadas=materias_aprobadas($pensum,$cedula,$grado);

		$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND grado='".$grado."' GROUP BY id ASC";
		$resultado = $conn->query($sql);


		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {  

				$lismat_cod_mat[$x1]=$fila['cod_mat'];			
				$descrip2[$x1]=$fila['descrip2'];
				$creditos[$x1]=$fila['creditos'];
				$semestre[$x1]=$fila['semestre'];			
				$nota_cat[$x1]=$fila['nota'];
				$divicion[$x1]=$fila['divicion'];
				$trayecto[$x1]=$fila['trayecto'];
				$aprobatori[$x1]=$fila['aprobatori'];
				$x1=$x1+1; 	

			}
		}


		for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 
			if(substr($lismat_cod_mat[$i], 1, 1)=="R"){
				calcular_resumida($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1],$electiva[$x1]);
				if($cantidad_materias==$materias_aprobadas AND $i==count($lismat_cod_mat)-1){
					return "true";            
				}
			}
		}
	}
}



function comprobar_ing($cedula,$grado)
{
	if($grado=="I" OR $grado=="L"){

		include "db.php";
		$query = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
		$result = mysqli_query($conn, $query);
		while($row = mysqli_fetch_array($result))
		{
			$carrera=$row["carrera"];
		}

		include('getpensum3.php');         
		$pensum=pensum($carrera);

		$cantidad_materias=cantidad_materias($pensum,$grado);
		$materias_aprobadas=materias_aprobadas($pensum,$cedula,$grado);

		$x1=0;

		$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND grado='".$grado."' GROUP BY id ASC";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 

				$lismat_cod_mat[$x1]=$fila['cod_mat'];			
				$descrip2[$x1]=$fila['descrip2'];
				$creditos[$x1]=$fila['creditos'];
				$semestre[$x1]=$fila['semestre'];			
				$nota_cat[$x1]=$fila['nota'];
				$divicion[$x1]=$fila['divicion'];
				$trayecto[$x1]=$fila['trayecto'];
				$aprobatori[$x1]=$fila['aprobatori'];
				$electiva[$x1]=$fila['electiva'];
				$grado2[$x1]=$fila['grado'];
				$x1=$x1+1; 			

			}
		}

		for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 
			if($trayecto[$i]==0 and $materias_aprobadas==$cantidad_materias and $grado2[$i]==$grado){

			}else{
				if(substr($lismat_cod_mat[$i], 1, 1)=="R"){
					calcular_resumida($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1],$electiva[$x1]);
					if($cantidad_materias==$materias_aprobadas){
						return "true";
						
					}			   
				}
			}
		}
	}
}

?>
