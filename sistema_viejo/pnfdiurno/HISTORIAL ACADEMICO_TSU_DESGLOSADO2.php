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
require ('fpdf.php');

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

class PDF extends FPDF
{

	function Encabezado()
	{
		$this->AddPage();
		date_default_timezone_set('America/Caracas');

		$DIA=date("d");
		$MES=date("m");
		$AÑO=date("Y");

		$hoy = date("d-m-Y");  	

		$pensum=$_POST["pensum"];
		$cedula=$_POST["cedula"];

		include "db.php";


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

		if($carrera!="G"){
			$Semestre="TRIMISTRE";
			$Sem="TRIM";
		}else{
			$Semestre="SEMESTRE";
			$Sem="SEM";
		}




		include('getpensum3_clase.php');
		$con = new carreras();
		$carrera_a1 = $con->carrera_larga($carrera);
		$carrera_a2 = $con->carrera_corta($carrera);

		$this->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
        //$this->SetFillColor(0,0,0);
		$this->SetFont('Arial','',8);


		$X1=5;
		$this->Image("LOGO.jpg" , 10 ,0+$X1, 43 , 25 , "jpg" ,"");

		$this->SetXY(15,0+$X1);
		$this->Cell(190, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'C', 0);
		$this->SetXY(15,4+$X1);
		$this->Cell(190, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'C', 0);

		$this->SetXY(15,4+$X1);
		$this->Cell(185, 5, $hoy, 0, 0, 'R', 0);

		$this->SetXY(15,8+$X1);
		$this->Cell(190, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'C', 0);

		$this->SetFont('Arial','B',10);
		$this->SetXY(15,15+$X1);
		$this->Cell(190, 5, utf8_decode("HISTORIAL ACADEMICO DESGLOSADO"), 0, 0, 'C', 0);
		$X1=8;
		$this->SetFont('Arial','',8);
		$this->SetXY(15,20+$X1);
		$this->Cell(20, 7, "CODIGO: ".$CODIGO, 0, 0, 'L', 0);

		$this->SetXY(50,20+$X1);
		$this->Cell(15, 7, "CEDULA: ".$cedula, 0, 0, 'L', 0);

		$this->SetXY(85,20+$X1);
		$this->Cell(50, 7, "NOMBRE: ".utf8_decode($NOMBRE), 0, 0, 'L', 0);

		$this->SetXY(160,20+$X1);
		$this->Cell(15, 7, "ACT.: ".$ACTIVIDAD, 0, 0, 'L', 0);

		$this->SetXY(180,20+$X1);
		$this->Cell(15, 7, "$Semestre: ".$SEMESTRE, 0, 0, 'L', 0);
		$X1=7;
		$this->SetXY(160,26+$X1);
		$this->Cell(15, 5, "PLAN: ".$PLAN, 0, 0, 'L', 0);

		$this->SetXY(180,26+$X1);
		$this->Cell(15, 5, "NIVEL: ".$NIVEL, 0, 0, 'L', 0); 

		$this->SetXY(15,26+$X1);
		$this->Cell(190, 5, utf8_decode("CARRERA: ".$carrera_a1), 0, 0, 'L', 0);
		$X1=5;
		$this->SetXY(15,30+$X1);
		$this->Cell(190, 5, utf8_decode("ASIGNATURAS CURSADAS"), 0, 0, 'C', 0);

		$this->SetFont('Arial','B',8);
		$this->SetXY(15,35+$X1);
		$this->Cell(20, 7, utf8_decode("CODIGO"), 1, 1, 'C', 0);

		$this->SetXY(35,35+$X1);
		$this->Cell(75, 7, utf8_decode("NOMBRE DE LA ASIGNATURA"), 1, 1, 'C', 0);

		$this->SetXY(110,35+$X1);
		$this->Cell(10, 7,$Sem, 1, 1, 'C', 0);        
		$this->SetXY(120,35+$X1);        
		$this->Cell(10, 7, "UC", 1, 1, 'C', 0);
		$this->SetXY(130,35+$X1);
		$this->Cell(10, 7, "VC", 1, 1, 'C', 0);
		$this->SetXY(140,35+$X1);
		$this->Cell(15, 7, "NOTAS", 1, 1, 'C', 0);

		$this->SetXY(155,35+$X1);
		$this->Cell(15, 7, "LAPSO", 1, 1, 'C', 0);

		$this->SetXY(170,35+$X1);
		$this->Cell(15, 7, "TIPO", 1, 1, 'C', 0);

		$this->SetXY(185,35+$X1);
		$this->Cell(15, 7, "CUR.", 1, 1, 'C', 0);


		$X1=5;

	}




}// fin de la clase





$pdf=new PDF();
$pdf->Encabezado(); 


$X1B=12;


$t=0;


$cedula=$_POST["cedula"];

include "db.php";



$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$resultado = $conn->query($sql);

if (!$resultado) {	
	exit;
}
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 

		$CODIGO=$fila['codigo'];  
		$carrera=$fila['carrera'];
		$MENCION=$fila['mencion'];
		$PLAN=$fila['plan'];
		$NOMBRE=$fila['nombre'];
		$SEMESTRE=$fila['semestre'];
		$ACTIVIDAD=$fila['actividad'];
		$NIVEL=$fila['nivel'];
	}
}

$pensum=$_POST["pensum"];
$x1=0;
$APROBADOS=0;
$FALTANTES=0;


$grado=$_POST["grado"];

if($grado=="T"){
	$tra1=1;
	$tra2=2;
}else{
	$tra1=3;
	$tra2=4;
}

$sql = "SELECT notas.cod_mat,notas.lapso,notas.nota,lismat.trayecto,lismat.semestre,lismat.creditos,lismat.descrip2,notas.tiplap,notas.cod_doc,notas.seccion FROM notas,lismat WHERE notas.codigo='".$cedula."' AND notas.cod_mat=lismat.cod_mat  AND lismat.pensum='".$pensum."' AND lismat.grado='".$grado."' ORDER BY lismat.id ASC";
$resultado = $conn->query($sql);
$x2=0;

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {  		
		
////////////////////////////////////////////////////////////////////////	   

		if($fila['trayecto']==0 AND $t==0){	
			$pdf->SetFont('Arial','B',8);	
			$pdf->SetXY(15,35+$X1B);
			$pdf->Cell(180, 4, utf8_decode("Trayecto:  0"), 0, 0, 'L', 0);
			if($grado=="T"){
				$t=1;
			}else{
				$t=3;
			}
			
			$X1B=$X1B+4;
		}


		if($fila['trayecto']==$tra1 && $t==$tra1){

			$pdf->SetFont('Arial','B',8);			
			$pdf->SetXY(15,35+$X1B);
			$pdf->Cell(180, 4, utf8_decode("Trayecto:  ")-.$tra1, 0, 0, 'L', 0);
			$t=$t+1;
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY(110,35+$X1B);

			$X1B=$X1B+4;
			$pdf->SetFont('Arial','',8);

		}						

		if($fila['trayecto']==$tra2 && $t==$tra2){

			$pdf->SetFont('Arial','B',8);				
			$pdf->SetXY(15,35+$X1B);
			$pdf->Cell(180, 4, utf8_decode("Trayecto:  ").$tra2, 0, 0, 'L', 0);
			$t=$t+1;

			$X1B=$X1B+4;
			$pdf->SetFont('Arial','',8);

		}




////////////////////////////////////////////////////////////////////////
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY(15,35+$X1B);
		$pdf->Cell(20, 4,$fila['cod_mat'], 1, 1, 'C', 0);

		$pdf->SetXY(35,35+$X1B);
		$pdf->Cell(75, 4,utf8_decode(substr($fila['descrip2'], 0, 39)), 1, 1, 'C', 0);

		$pdf->SetXY(110,35+$X1B);
		$pdf->Cell(10, 4,$fila['semestre'], 1, 1, 'C', 0);        
		$pdf->SetXY(120,35+$X1B);             
		$pdf->Cell(10, 4, $fila['creditos'], 1, 1, 'C', 0);
		$pdf->SetXY(130,35+$X1B);
		$pdf->Cell(10, 4, 1, 1, 1, 'C', 0);


		$pdf->SetXY(140,35+$X1B);	
		$pdf->Cell(15, 4, $fila['nota'], 1, 1, 'C', 0);
		
		
		$pdf->SetXY(155,35+$X1B);
		$pdf->Cell(15, 4, $fila['lapso'], 1, 1, 'C', 0);

		
		$pdf->SetXY(170,35+$X1B);
		$pdf->Cell(15, 4, $fila['tiplap'], 1, 1, 'C', 0);


		$pdf->SetXY(185,35+$X1B);
		$pdf->Cell(15, 4,"", 1, 1, 'C', 0);




		$X1B=$X1B+4;			
		$x2=$x2+1;


	}
}




$pdf->SetFont('Arial','',8);
$pdf->SetXY(15,37+$X1B);
$pdf->Cell(50, 7, "RESUMEN:", 0, 0, 'L', 0);
$pdf->SetXY(120,37+$X1B);
$pdf->Cell(50, 7, "Emitido por: ".$NOMBRE_USER, 0, 0, 'L', 0);



$pdf->SetXY(15,42+$X1B);
$pdf->Cell(50, 7, "Indice de Rendimiento Academico: ".$ira, 0, 0, 'L', 0);

$pdf->SetXY(100,42+$X1B);
$pdf->Cell(50, 7, "GENERAL: ", 0, 0, 'L', 0);

$pdf->SetXY(130,42+$X1B);
$pdf->Cell(50, 7, "APROBADOS: ".$APROBADOS, 0, 0, 'L', 0);

$pdf->SetXY(160,42+$X1B);
$pdf->Cell(50, 7, "FALTANTES: ".$FALTANTES, 0, 0, 'L', 0);

$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.001);
$pdf->Line(15, 43+$X1B, 200,43+$X1B);

$pdf->SetXY(100,47+$X1B);
$pdf->Cell(50, 7, "EQUIVALENTES: ", 0, 0, 'L', 0);

$pdf->SetXY(130,47+$X1B);
$pdf->Cell(50, 7, "MAX. A CURSAR: ".$MAX_A_CURSAR, 0, 0, 'L', 0);

$pdf->SetXY(30,47+$X1B);
$pdf->Cell(50, 7, $nota_sum, 0, 0, 'L', 0);

$pdf->Line(15, 53+$X1B, 200,53+$X1B);

$pdf->SetXY(15,52+$X1B);
$pdf->Cell(50, 7, "A= Acreditado", 0, 0, 'L', 0);


$pdf->SetFont('Arial','B',7);
$pdf->SetXY(15,56+$X1B);
$pdf->Cell(50, 7, utf8_decode("Institución autorizada para gestionar el Programa Nacional de Formación según Gaceta oficial de la República Bolivariana de Venezuela N° 39721"), 0, 0, 'L', 0);

$pdf->SetXY(15,60+$X1B);
$pdf->Cell(50, 7, utf8_decode("de fecha 26 de Julio del 2011"), 0, 0, 'L', 0);


$pdf->SetFont('Arial','',11);
$pdf->SetXY(22,68+$X1B);
$pdf->Cell(180, 5, utf8_decode("Este Documento"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',13);
$pdf->SetXY(52,68+$X1B);
$pdf->Cell(180, 5, utf8_decode("NO ES VALIDO"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(86,68+$X1B);
$pdf->Cell(180, 5, utf8_decode("sin la firma y Sello del Departamento de Control De Estudios"), 0, 0, 'L', 0);

$pdf->Output();
?>
