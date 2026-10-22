<?php

function select_nota_E($cedula,$materia,$aprobatori,$veses)
{ 						

	global $nota_r,$lapso_r,$tiplap_r;
	include "db.php";	

	$materia=substr($materia, 0, 1)."E".substr($materia, 2, 2).substr($materia, 4, 1);

	echo "Cedula: ".$cedula." Materia: ".$materia." Veses: ".$veses."<br>";



	$sql = "SELECT MAX(`lapso`) AS 'max_lapso' FROM `notas` WHERE `codigo`='".$cedula."' AND `cod_mat`='".$materia."'";			
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$max_lapso=$row['max_lapso'];					

		} 
	}

	$sql = "SELECT lapso,nota,tiplap FROM `notas` WHERE `codigo`='".$cedula."' AND `cod_mat`='".$materia."' and `lapso`='".$max_lapso."' and `tiplap`='E'";			
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$nota[$i]=$row['nota'];
			$lapso[$i]=$row['lapso'];
			$tiplap[$i]=$row['tiplap'];
			$cantidad=$i;
				// echo "***".$i."***"."<br>";
				// echo "Nota E: ".$nota[$i]."<br>";
				// echo "Lapso E: ".$lapso[$i]."<br>";
				// echo "Tiplap E: ".$tiplap[$i]."<br>";	
				// echo "********************************"."<br>";
		} 
	}

	

	

	if($nota_r=="IN" and strlen($nota_r)==2){

	}else{
		if($nota_r<>"IN"){
			$nota_r=round($nota_r, 0, PHP_ROUND_HALF_UP);
		}
		if(strlen($nota_r)==1){
			$nota_r= sprintf( "%2d".$nota_r, "" ); 
		}  
		if($nota_r == 00){
			$nota_r = 0; 
		}  
	}

	

}


function select_nota($cedula,$materia,$aprobatori,$veses)
{ 						

	global $nota_r,$lapso_r,$tiplap_r;
	include "db.php";	

    $carrera=substr($materia, 0, 1);

	$sql = "SELECT * FROM `notas` WHERE `codigo`='".$cedula."' AND `carrera`='".$carrera."'";			
	$result = $conn->query($sql);
    $y=1;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id[$y]=$row['id'];
			$codigo[$y]=$row['codigo'];
			$cod_mat[$y]=$row['cod_mat'];
			$carrera[$y]=$row['carrera'];
			$nota[$y]=$row['nota'];
			$lapso[$y]=$row['lapso'];
			$tiplap[$y]=$row['tiplap'];
			$cod_doc[$y]=$row['cod_doc'];
			$cod_usu[$y]=$row['cod_usu'];
			$acu[$y]=$row['acu'];
			$seccion[$y]=$row['seccion'];
			$electiva[$y]=$row['electiva'];
			$y=$y+1;
		} 
	}






	for ($i=0; $i <=$veses ; $i++) { 

		$materia=substr($materia, 0, 1).$i.substr($materia, 2, 2).substr($materia, 4, 1);


		$sql = "SELECT MAX(`lapso`) AS 'max_lapso' FROM `notas` WHERE `codigo`='".$cedula."' AND `cod_mat`='".$materia."'";			
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				$max_lapso=$row['max_lapso'];					

			} 
		}

		$sql = "SELECT lapso,nota,tiplap FROM `notas` WHERE `codigo`='".$cedula."' AND `cod_mat`='".$materia."' and `lapso`='".$max_lapso."'";			
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$nota[$i]=$row['nota'];
				$lapso[$i]=$row['lapso'];
				$tiplap[$i]=$row['tiplap'];
				$cantidad=$i;				
			} 
		}

	}//fin for

	switch ($veses) {		
		case 1:	
		$nota_r=$nota[$cantidad];
		$lapso_r=$lapso[$cantidad];
		$tiplap_r=$tiplap[$cantidad];		
		break;
		case 2:
		$nota_r=($nota[1]+$nota[2])/2;
		$lapso_r=$lapso[2];
		$tiplap_r=$tiplap[2];
		break;
		case 3:
		$nota_r=($nota[1]+$nota[2]+$nota[3])/3;
		$lapso_r=$lapso[3];
		$tiplap_r=$tiplap[3];
		break;		
	}// FIN switch

	if($nota_r=="IN" and strlen($nota_r)==2){

	}else{
		if($nota_r<>"IN"){
			$nota_r=round($nota_r, 0, PHP_ROUND_HALF_UP);
		}
		if(strlen($nota_r)==1){
			$nota_r= sprintf( "%2d".$nota_r, "" ); 
		}  
		if($nota_r == 00){
			$nota_r = 0; 
		}  
	}

	

}






// select_nota_E("V19566267","MEBVC","12",1);
// echo "***E***"."<br>";
// echo "Nota: ".$nota_r."<br>";
// echo "Lapso: ".$lapso_r."<br>";
// echo "Tiplap: ".$tiplap_r."<br>";

select_nota("V11095250","I0ALC","12",3);
echo "Nota: ".$nota_r."<br>";
echo "Lapso: ".$lapso_r."<br>";
echo "Tiplap: ".$tiplap_r."<br>";