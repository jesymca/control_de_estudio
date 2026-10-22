<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
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


require ('fpdf.php'); 
require ('num2letras.php');

require ("aud.php");
auditar("ACTAS_FINAL",$_POST["cod_mat"]." - ".$_POST["lapso"]);


class PDF extends FPDF
{

	function Encabezado()
	{

		$this->AddPage();
		$this->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
		date_default_timezone_set('America/Caracas');
		$fechaActual = date('d-m-Y');   
		$DIA=date("d");
		$MES=date("m");
		$AÑO=date("Y");

		date_default_timezone_set('UTC');
		$hoy = date("d-m-Y");
		

		

        //$this->SetFillColor(0,0,0);
		$this->SetFont('Arial','B',12);


		$X1=5;
		$this->Image("logoiutpc2.jpg" , 10 ,0+$X1, 45 , 25 , "jpg" ,"");

		$this->SetXY(54,0+$X1);
		$this->Cell(136, 5, utf8_decode("REPÚBLICA BOLIVARIANA DE VENEZUELA"), 0, 0, 'L', 0);
		$this->SetFont('Arial','',10);
		$this->SetXY(54,4+$X1);
		$this->Cell(136, 5, utf8_decode("MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA"), 0, 0, 'L', 0);
		$this->SetXY(54,8+$X1);
		$this->Cell(136, 5, utf8_decode("CIENCIA Y TECNOLOGÍA"), 0, 0, 'L', 0);

		$this->SetXY(54,15+$X1);
		$this->Cell(136, 5, utf8_decode("INSTITUTO UNIVERSITARIO DE TECNOLOGIA"), 0, 0, 'L', 0);
		$this->SetXY(54,19+$X1);
		$this->Cell(136, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'L', 0);
		$this->SetXY(54,23+$X1);
		$this->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);

		$this->SetFont('Arial','B',10);
		
		$this->SetXY(55,35+$X1);
		$this->Cell(100, 7,"LISTADO DE ALUMNOS MENORES DE EDAD", 0, 0, 'C', 0);

		$this->SetFont('Arial','',8);   
		$this->SetXY(167,45+$X1);
		$this->Cell(20, 5, utf8_decode("FECHA: ").$fechaActual, 0, 0, 'L', 0);


		$this->SetFont('Arial','B',8);
        $X1=-5;
		$this->SetXY(15,60+$X1);
		$this->Cell(179, 5, "DATOS DEL ALUMNO", 1, 0, 'C', 0);


		$this->SetXY(15,65+$X1);
		$this->Cell(7, 5, "#", 1, 1, 'C', 0);

		$this->SetXY(22,65+$X1);
		$this->Cell(17, 5, "Cedula", 1, 1, 'C', 0);

		$this->SetXY(39,65+$X1);
		$this->Cell(90, 5, "Nombre del alumno", 1, 1, 'C', 0);

		$this->SetXY(129,65+$X1);
		$this->Cell(30, 5,"Carrera", 1, 1, 'C', 0);
		
		$this->SetXY(159,65+$X1);
		$this->Cell(35, 5, "Fecha de Nacimiento", 1, 1, 'C', 0);
		
	}

	
	
}

$desde=$_POST["desde"];
$hasta=$_POST["hasta"];        



$pdf=new PDF();
$pdf->Encabezado();	

include "db.php";

$sql = "SELECT `cedula`,`nombre`,`carrera`,`fechanac` FROM `alumno` WHERE `fechanac` LIKE '%$desde%' OR `fechanac` LIKE '%$hasta%'";
$resultado = $conn->query($sql);

$X1B=-7;
$IN=0;
$APRO=0;
$REPR=0;

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {   
		switch ($fila["carrera"])  {
			case "M":
			$Carrera_a1="P.N.F. MECANICA";
			$Carrera_a2="MECANICA";
			break;
			case "T":
			$Carrera_a1="P.N.F. MANTENIMIENTO";
			$Carrera_a2="MANTENIMIENTO";
			break;
			case "E":
			$Carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
			$Carrera_a2="MATERIALES";
			break;
			case "I":
			$Carrera_a1="P.N.F. INFORMATICA";
			$Carrera_a2="INFORMATICA";
			break;
			case "G":
			$Carrera_a1="P.N.F. TURISMO";
			$Carrera_a2="TURISMO";
			break;   
			case "O":
			$Carrera_a1="P.I.F. MECANICA TERMICA";
			$Carrera_a2="TERMICA";
			break;
			case "A":
			$Carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
			$Carrera_a2="AUTOMOTRIZ";
			break;
		}

		$pdf->SetFont('Arial','',8);

		$X0=$X0+1;
		$X1B=$X1B+5;

		$pdf->SetXY(15,67+$X1B);
		$pdf->Cell(7, 5, $X0, 1, 1, 'C', 0);

		$pdf->SetXY(22,67+$X1B);		
		$pdf->Cell(17, 5, $fila['cedula'], 1, 1, 'L', 0);

		$pdf->SetXY(39,67+$X1B);
		$pdf->Cell(90, 5, utf8_decode($fila['nombre']), 1, 1, 'L', 0);

		$pdf->SetXY(129,67+$X1B);
		$pdf->Cell(30, 5, $Carrera_a2, 1, 1, 'C', 0);

		$pdf->SetXY(159,67+$X1B);
		$pdf->Cell(35, 5, $fila["fechanac"], 1, 1, 'C', 0);

		if($X0>40){

			$pdf->Encabezado();
			$X1B=5;

		}

	}
}

$pdf->Output();


?>
