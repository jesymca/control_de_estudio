<?php

class calcular_aprobado
{


	function calcular_resumida2($pensum,$X1B,$lismat_cod_mat,$cedula,$X1B,$descrip2,$semestre,$creditos,$trayecto,$aprobatori,$divicion)
	{ 			

		include "db.php";
		global $nota_r2,$X1B,$X0,$esta,$FALTANTES,$APROBADOS;	

		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);

		$materia0=$carrera."0".$materia.$plan;
		$materia1=$carrera."1".$materia.$plan;
		$materia2=$carrera."2".$materia.$plan;
		$materia3=$carrera."3".$materia.$plan;
		$materia4=$carrera."R".$materia.$plan;			

		$lapso0 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
		$lapso1 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
		$lapso2 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
		$lapso3 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");

		$nota0 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
		$nota1 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
		$nota2 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
		$nota3 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");

		if($lapso3<>""){
			$lapso=$lapso3;

		}elseif ($lapso2<>"") {
			$lapso=$lapso2;

		}elseif ($lapso1<>"") {
			$lapso=$lapso1;

		}else{
			$lapso=$lapso0;				
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

		if($nota_r2<>"IN"){
			$nota_r2=round($nota_r2, 0, PHP_ROUND_HALF_UP);
		}

		if(strlen($nota_r2)==1){
			$nota_r2= sprintf( "%2d".$nota_r2, "" ); 
		}  

		if($nota_r2 == 00){
			$nota_r2 = 0; 
		}  

		if ($nota_r2<>"") {          

			if ($this->verificar_per($cedula,$lismat_cod_mat)=="true") {

				if ($nota_r2>5 and $nota_r2<$aprobatori and $aprobatori<>16) {    
					$nota_r2=$this->calcular_per($cedula,$lismat_cod_mat,$nota_r2);                  
					$nota_r2=round($nota_r2, 0, PHP_ROUND_HALF_UP);
				}
			} 

			if ($this->verificar_intensivo($cedula,$lismat_cod_mat)=="true") {
				if ($nota_r2<$aprobatori and $aprobatori<>16) {    
					$nota_r2=$this->intensivo($cedula,$lismat_cod_mat);       		
				}
			}

			if ($this->verificar_ext($cedula,$lismat_cod_mat)=="true") {
				if ($nota_r2<$aprobatori and $aprobatori<>16) {    
					$nota_r2=$this->verificar_ext($cedula,$lismat_cod_mat);       		
				}
			}


			if(strlen($nota_r2)==1){
				$nota_r2= sprintf( "%2d".$nota_r2, "" ); 
			}


			return $nota_r2;

		}
	}


	function verificar_per($cedula,$lismat_cod_mat)
	{ 			

		include "db.php";


		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);


		$materia_p=$carrera."P".$materia.$plan;

		$sql = "SELECT * FROM notas WHERE codigo='".$cedula."' and cod_mat='".$materia_p."'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$dat="true";
		}else{
			$dat="false";
		}              

		return $dat;             

	}

	function verificar_intensivo($cedula,$lismat_cod_mat)
	{ 			

		include "db.php";

		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);


		$materia_i=$carrera."T".$materia.$plan;

		$sql = "SELECT * FROM notas WHERE codigo='".$cedula."' and cod_mat='".$materia_i."'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$dat="true";
		}else{
			$dat="false";
		}              

		return $dat;             

	}


	function verificar_ext($cedula,$lismat_cod_mat)
	{ 			

		include "db.php";

		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);


		$materia_i=$carrera."E".$materia.$plan;

		$sql = "SELECT * FROM notas WHERE codigo='".$cedula."' and cod_mat='".$materia_e."'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$dat="true";
		}else{
			$dat="false";
		}              

		return $dat;             

	}

	function calcular_per($cedula,$lismat_cod_mat,$nota_r)
	{ 			

		include "db.php";


		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);


		$materia_p=$carrera."P".$materia.$plan;

		$sql = "SELECT * FROM notas WHERE codigo='".$cedula."' and cod_mat='".$materia_p."'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {				
				$nota=$row['nota'];		
			} 
		}



		$nota_p=$nota;
		$t30=($nota_r*100)/20;

		$t70=100-$t30;
		$t70_2=$nota_p*($t70/100);
		$dat=$nota_r+$t70_2;


		return $dat;        


	}
	

	function intensivo($cedula,$lismat_cod_mat)
	{ 			

		include "db.php";

		$materia=substr($lismat_cod_mat, 2, 2);
		$mate=substr($lismat_cod_mat, 1, 1);
		$carrera=substr($lismat_cod_mat, 0, 1);
		$plan=substr($lismat_cod_mat, 4, 1);


		$materia_t=$carrera."T".$materia.$plan;	

		$sql = "SELECT * FROM notas WHERE codigo='".$cedula."' and cod_mat='".$materia_t."'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {				
				$dat=$row['nota'];		
			} 
		}

		return $dat;  
	}



	function select_nota($tabla,$where)
	{ 	
		include "db.php";			
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where." ORDER BY lapso DESC";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['nota'];
				return $dat;
			} 
		}
	}

	function select_lapso($tabla,$where)
	{ 				
		include "db.php";
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where." ORDER BY lapso DESC";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['lapso'];
				return $dat;
			} 
		}
	}

	function select_tiplap($tabla,$where)
	{ 				
		include "db.php";
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['tiplap'];
				return $dat;
			} 
		}
	}

	function actualizar_ira($cedula,$ira)
	{
		include "db.php";
		$sql = "UPDATE `alumno` SET ira='".$ira."' WHERE cedula='".$cedula."'";
		$result = $conn->query($sql);
	}	

	function cantidad_materias($pensum,$grado)
	{

		include "db.php";
		
		$carrera=substr($pensum, 0, 1);		

		$sql = "SELECT * FROM `lismat` WHERE `pensum` LIKE '".$pensum."' AND `cod_mat` LIKE '%".$carrera."R%' AND grado='".$grado."'"; 
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
				

				if(substr($lismat_cod_mat2[$x1], 1, 1)=="R"){			
					$notas_res=$this->calcular_resumida2($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1]);
					if($notas_res>=$aprobatori[$x1]){
						$dat++;
					}
				}

				$x1=$x1+1; 			

			}
		}

		return $dat;  
	}


	function materias_aprobadas_trayecto_0($pensum,$cedula,$grado)
	{ 

		include "db.php";

		$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND grado='".$grado."' GROUP BY id ASC";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 
				
                    if ($fila['trayecto']==0){  
 
					$lismat_cod_mat2[$x1]=$fila['cod_mat'];			
					$descrip2[$x1]=$fila['descrip2'];
					$creditos[$x1]=$fila['creditos'];
					$semestre[$x1]=$fila['semestre'];			
					$nota_cat[$x1]=$fila['nota'];
					$divicion[$x1]=$fila['divicion'];
					$trayecto[$x1]=$fila['trayecto'];
					$aprobatori[$x1]=$fila['aprobatori'];
				

				if(substr($lismat_cod_mat2[$x1], 1, 1)=="R"){			
					$notas_res=$this->calcular_resumida2($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1]);
					if($notas_res>=$aprobatori[$x1]){
						$dat++;
					}
				}

				$x1=$x1+1; 

				}			

			}
		}

        if($dat>=2){
        	return "true";  
        }else{
        	return "false";
        } 
		
	}

	function verificar($pensum,$cedula,$grado)
	{ 

		$cantidad_materias=$this->cantidad_materias($pensum,$grado);
		$materias_aprobadas=$this->materias_aprobadas($pensum,$cedula,$grado);

		if($materias_aprobadas<>0 and $cantidad_materias<>0){

			if($materias_aprobadas==$cantidad_materias){

				return "true";		 	

			}else{

				return "false";
			}

		}else{
			return "No existe ese grado";
		}

	}


	function materia_proyecto($pensum,$cedula,$grado,$trayecto)
	{ 

		include "db.php";

		$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND trayecto='".$trayecto."' AND grado='".$grado."' GROUP BY id ASC";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 

				if ($fila['aprobatori']==16){                         
					$lismat_cod_mat2[$x1]=$fila['cod_mat'];			
					$descrip2[$x1]=$fila['descrip2'];
					$creditos[$x1]=$fila['creditos'];
					$semestre[$x1]=$fila['semestre'];			
					$nota_cat[$x1]=$fila['nota'];
					$divicion[$x1]=$fila['divicion'];
					$trayecto[$x1]=$fila['trayecto'];
					$aprobatori[$x1]=$fila['aprobatori'];


					if(substr($lismat_cod_mat2[$x1], 1, 1)=="R"){			
						$notas_res=$this->calcular_resumida2($pensum,$X1B,$lismat_cod_mat2[$x1],$cedula,$X1B,$descrip2[$x1],$semestre[$x1],$creditos[$x1],$trayecto[$x1],$aprobatori[$x1],$divicion[$x1]);
						if($notas_res>=$aprobatori[$x1]){
							$dat="true";
						}
					}

					$x1=$x1+1; 	
				}		

			}
		}       

		return $dat;  
	}



}// fin de la clase



$calcular=new calcular_aprobado();





// $pensum=$_POST["pensum"];
// $grado=$_POST["grado"];
// $cedula=$_POST["cedula"];

$pensum="IXC";
$grado="T";
$cedula="V11095250";
$trayecto="2";

//echo $calcular->verificar($pensum,$cedula,$grado);

//echo $calcular->materia_proyecto($pensum,$cedula,$grado,$trayecto)."<BR>";

echo $calcular->materias_aprobadas_trayecto_0($pensum,$cedula,$grado);



?>
