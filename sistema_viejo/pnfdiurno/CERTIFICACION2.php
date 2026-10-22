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


$pensum=$_POST["pensum"];
$cedula=$_POST["cedula"];
$grado=$_POST["grado"];

$pensum="EXB";
$cedula="V15225169";
$grado="I";


include('HISTORI.php');
$x=new PDF();
$cantidad_materias=$x->cantidad_materias($pensum,$grado);
$materias_aprobadas=$x->materias_aprobadas($pensum,$cedula,$grado);
$cantidad_materias_tsu=$x->cantidad_materias($pensum,"T");
$materias_aprobadas_tsu=$x->materias_aprobadas($pensum,$cedula,"T");



if($materias_aprobadas==$cantidad_materias AND $grado=="T" OR $materias_aprobadas==$cantidad_materias AND $grado=="I" OR $materias_aprobadas==$cantidad_materias AND $grado=="L"){

	date_default_timezone_set('America/Caracas');
	$DIA=date("d");
	$MES=date("m");
	$AÑO=date("Y");

	$hoy = date("d-m-Y");  

	include "db.php";

	$sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
	$resultado = $conn->query($sql);

	$CREDSUM=0;

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 
			$NOMBRE_USER=$fila['nombre'];              
		}
	}

	$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
	$resultado = $conn->query($sql);

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 		
			$carrera=$fila["carrera"];
		} 
	} 

	$sql = "SELECT max(lapso) as maxlapso FROM notas WHERE codigo= '".$cedula."'";
	$resultado = $conn->query($sql);
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {		
			$lapso_actual= $fila['maxlapso'];
		}  
	}

	$pdf=new PDF();
	$pensum = $pdf->pensum($carrera);
	$carrera_a1 = $pdf->carrera_larga($carrera);
	$carrera_a2 = $pdf->carrera_corta($carrera);
	$pdf->Encabezado_certificacion($cedula,$carrera_a1); 

///////////////////////////////////

	$irx=1;
	$X1B=-42;
	$t=0;		

	$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
	$resultado = $conn->query($sql);

	if (!$resultado) {	
		exit;
	}
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 

			$CODIGO=$fila['codigo'];  
			$cedula=$fila['cedula']; 	    
			$carrera=$fila['carrera'];
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
		$lap1=1;
		$lap2=2;
	}else{
		$lap1=3;
		$lap2=4;
	}

	if($pensum=="EXC" AND $grado=="I"){
		$t=3;
	}else{
		$t=0;	
	}

	$cantidad_materias=$pdf->cantidad_materias($pensum,$grado);
	$materias_aprobadas=$pdf->materias_aprobadas($pensum,$cedula,$grado);

	$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' AND grado='".$grado."' AND SUBSTRING(cod_mat,2,1)='R' GROUP BY id ASC";
	$resultado = $conn->query($sql);
	$x1=0;
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
			$cod_mat_libro_rector[$x1]=$fila['cod_mat_libro_rector'];
			$x1=$x1+1; 	
		}
	}
	$esta=="NO";

	$t0=0;
	$t1=0;
	$t2=0;
	if($pensum=="EXC"){
		$entre_lineado=3;
	}else{
		$entre_lineado=4;	
	}
	$conteo=1;

	for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 
		if($grado=="T"){
			if($trayecto[$i]==0 AND $t==0){	
				$pdf->SetFont('Arial','B',8);	
				$pdf->SetXY(20,35+$X1B);
				$pdf->Cell(180, 4, utf8_decode("Trayecto:  0"), 0, 0, 'L', 0);
				$t=$t+1;		
				$X1B=$X1B+$entre_lineado;
				$trayecto_O="true";			
			}
		}else{

				if($cantidad_materias_tsu==$materias_aprobadas_tsu and $t==0){
					$pdf->SetFont('Arial','B',8);	
					$pdf->SetXY(20,35+$X1B);				
					$pdf->Cell(180, 4, utf8_decode("Trayecto:  0"), 0, 0, 'L', 0);
					$X1B=$X1B+$entre_lineado;
					$pdf->Line(13, 34.5+$X1B, 199, 34.5+$X1B);						
					$trayecto_O="true";	
					$t=3;				
				}else{
					$trayecto_O="false";
					$t=3;
				}
								
			
		}
		if($trayecto[$i]==$lap1 and $t==$lap1){		
			
			if($trayecto_O=="true"){
				if($uc_0<>0){
					$ira_0_t=round($ira_0/$uc_0);
				}
				$pdf->SetXY(110,36.2+$X1B);
				$pdf->Cell(180, 4, "IRA del Trayecto: ".$ira_0_t." Total UC del Trayecto: ".$uc_0, 0, 0, 'L', 0);
			}
			
            $X1B=$X1B+1.5;
			$pdf->SetFont('Arial','B',8);
			$pdf->Line(13, 34.5+$X1B, 199, 34.5+$X1B); 			
			$pdf->SetXY(20,35+$X1B);
			$pdf->Cell(180, 4, utf8_decode("Trayecto:  ").$lap1, 0, 0, 'L', 0);
			$t++;
			$lap1++;
			$X1B_0=$X1B_0+$X1B;
			$X1B=$X1B+$entre_lineado;		
		}	

		if($trayecto[$i]==$lap2 && $t==$lap2){ 
			$X1B=$X1B+1.5; 	         
			$pdf->Line(13, 34.5+$X1B, 199, 34.5+$X1B);           
			$pdf->SetFont('Arial','B',8);				
			$pdf->SetXY(20,35+$X1B);
			$pdf->Cell(180, 4, utf8_decode("Trayecto:  ").$lap2, 0, 0, 'L', 0);
			$t++;
			$lap2++;
			$X1B_1=$X1B_1+$X1B;
			$X1B=$X1B+$entre_lineado;		
		}

		if(substr($lismat_cod_mat[$i], 1, 1)=="R"){  
          
			if($grado=="T"){
				$pdf->calcular_resumida($pensum,$X1B,$lismat_cod_mat[$i],$cedula,$X1B,$descrip2[$i],$semestre[$i],$creditos[$i],$trayecto[$i],$aprobatori[$i],$divicion[$i],$cod_mat_libro_rector[$i],$electiva[$i],"true","C");
			}else{
				  
				if($trayecto[$i]==0 and $materias_aprobadas==$cantidad_materias){
				}else{
					$pdf->calcular_resumida($pensum,$X1B,$lismat_cod_mat[$i],$cedula,$X1B,$descrip2[$i],$semestre[$i],$creditos[$i],$trayecto[$i],$aprobatori[$i],$divicion[$i],$cod_mat_libro_rector[$i],$electiva[$i],"true","C");
				}
				
			} 
			if($trayecto[$i]==0){
				$IRA_0=$creditos[$i]*$nota_r;				
				$IRA_0B=$IRA_0B+$IRA_0;
				$uc_0=$uc_0+$creditos[$i];				
				$ira_0=$IRA_0B; 			
			}		
			
			if($trayecto[$i]==$lap1){			
				$IRA_1=$creditos[$i]*$nota_r;				
				$IRA_1B=$IRA_1B+$IRA_1;
				$uc_1=$uc_1+$creditos[$i];				
				$ira_1=$IRA_1B; 			
			} 

			if($trayecto[$i]==$lap2){			
				$IRA_2=$creditos[$i]*$nota_r;				
				$IRA_2B=$IRA_2B+$IRA_2;
				$uc_2=$uc_2+$creditos[$i];				
				$ira_2=$IRA_2B; 
			} 
		}

		if($esta=="NO"){
			if(substr($lismat_cod_mat[$i], 1, 1)=="R"){
				$pdf->SetFont('Arial','',8);


				$pdf->SetXY(12,35+$X1B);
				$pdf->Cell(20, 4, $cod_mat_libro_rector[$i], 0, 0, 'L', 0);

				$pdf->SetXY(28,35+$X1B);
				$pdf->Cell(20, 4,$lismat_cod_mat[$i], 0, 0, 'C', 0);


				$pdf->SetXY(45,35+$X1B);
				$pdf->Cell(75, 4,substr(utf8_decode($descrip2[$i]), 0, 39), 0, 0, 'C', 0);

				$pdf->SetXY(118,35+$X1B);
				$pdf->Cell(10, 4,$semestre[$i], 0, 0, 'C', 0); 

				$pdf->SetXY(133,35+$X1B);             
				$pdf->Cell(10, 4, $creditos[$i], 0, 0, 'C', 0);

				if($carrera=="G" and $lismat_cod_mat[$i]=="GRBSC" OR $lismat_cod_mat[$i]=="GRAYC"){
					if($materias_aprobadas==$cantidad_materias){
						$pdf->SetXY(142,35+$X1B);	
						$pdf->Cell(15, 4, "AP", 0, 0, 'C', 0);	
						$pdf->SetXY(155,35+$X1B);
						$pdf->Cell(15, 4, $lapso_actual, 0, 0, 'C', 0);			
						$pdf->SetXY(170,35+$X1B);
						$pdf->Cell(15, 4, "", 0, 0, 'C', 0);
						$X1B=$X1B+$entre_lineado;
						$X0=$X0+1;
					}
				}else{
					$pdf->SetXY(140,35+$X1B);			
					$pdf->Cell(15, 4, "", 0, 0, 'C', 0);
					$pdf->SetXY(135,35+$X1B);
					$pdf->Cell(15, 4, "", 0, 0, 'C', 0);
					$pdf->SetXY(170,35+$X1B);
					$pdf->Cell(15, 4, "", 0, 0, 'C', 0);
					$X1B=$X1B+$entre_lineado;
					$X0=$X0+1;
				}

				if($X0>45){
					$pdf->Encabezado_certificacion($cedula,$carrera_a1);
					$X1B=62;
					$X0=0;
				}
			}
		}
	}

	// $ira_0=round($IRA_0B, 3);
	// if(strlen($ira_0)==2){
	// 	$ira_0=$ira_0.".00"; 
	// } 

	if($pensum=="EXC"){
		$pdf->SetFont('Arial','',7);
	}else{
		$pdf->SetFont('Arial','',8);
	} 

	
	// if(strlen($ira_1)==2){
	// 	$ira_1=$ira_1.".00"; 
	// } 

	$ira_1=round($IRA_1B, 3);

	$pdf->SetXY(110,35+$X1B_1);
	if($uc_1<>0){
		$ira_1_t=round($ira_1/$uc_1);
	}

	$pdf->Cell(180, 4, "IRA del Trayecto: ".$ira_1_t." Total UC del Trayecto: ".$uc_1, 0, 0, 'L', 0);

	$pdf->pie_de_pagina_CERTIFICACION($X1B,$carrera_a2,$grado);

	// $ira_2=round($IRA_2B, 3);
	// if(strlen($ira_2)==2){
	// 	$ira_2=$ira_2.".00"; 
	// } 

	if($pensum=="EXC"){
		$pdf->SetFont('Arial','',7);
	}else{
		$pdf->SetFont('Arial','',8);
	} 
	$X1B=$X1B+1.5;
	$pdf->SetXY(110,35+$X1B);
	if($uc_1<>0){
		$ira_2_t=round($ira_2/$uc_2);
	}
	$pdf->Cell(180, 4, "IRA del Trayecto: ".$ira_2_t." Total UC del Trayecto: ".$uc_2, 0, 0, 'L', 0);

	$X1B=$X1B+6;
	$A_t=$A0+$A1+$A2;

	$uc_t=$uc_0+$uc_1+$uc_2;
	if($uc_t<>0){    
		$ira_t=($ira_0+$ira_1+$ira_2)/$uc_t;
	}
	// if(strlen($ira_t)>5){
	// 	$ira_t= sprintf( "%2d".$ira_t, "" ); 
	// }
	$pdf->SetXY(10,35+$X1B);
	$pdf->Cell(180, 7, "Unidades de Creditos Cursadas y Aprobadas: ".$uc_t."             Indice de Rendimiento Academico: ".round($ira_t), 0, 0, 'C', 0);
	$pdf->SetXY(25,35+$X1B);
	$pdf->Cell(150, 7, utf8_decode(""), 1, 0, 'C', 0);

	$pdf->Output();

}else{
	header("Location: msg_no_graduado.php");
}

?>