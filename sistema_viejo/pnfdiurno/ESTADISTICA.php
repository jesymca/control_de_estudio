<?php


require ('fpdf.php'); 
require ('num2letras.php');


$pdf=new PDF();
$pdf->AddPage();

date_default_timezone_set('America/Caracas');

$DIA=date("d");
$MES=date("m");
$AÑO=date("Y");	
$hoy = date("d-m-Y");


$pensum=substr($_POST["pensum"], 0, 1);

switch ($pensum)  {
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

include('db.php');


$pensum=$_POST["pensum"];

$sql = "SELECT * FROM `lismat` WHERE `pensum`='$pensum'";
$resultado = $conn->query($sql); 

$X1B=5;

$pdf->SetFont('Arial','',8);
if ($resultado->num_rows > 0) {
while($fila = $resultado->fetch_assoc()) { 

	$pdf->SetXY(15,42+$X1B);
	$pdf->Cell(20, 7, $COD_MAT, 1, 1, 'C', 0);
	
	$X1B=$X1B+7;

}
}

$pdf->Output();


?>
