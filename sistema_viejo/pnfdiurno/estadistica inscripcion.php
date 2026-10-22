<?php

$pensum=$_POST["pensum"];
$grado = $_POST['grado'];
$lapso = $_POST['lapso'];


switch ($pensum) {	
	case 'TXC':
	$seccion="50";
	break;
	case 'GXC':
	$seccion="60";
	break;
	case 'MXC':
	$seccion="10";
	break;	
	case 'EXC':
	$seccion="20";
	break;
	case 'IXC':
	$seccion="70";
	break;
	case 'CXC':
	$seccion="80";
	break;	
}
$primera_seccion=substr($seccion, 0, 1);

include('/Classes/class_api.php');
$pdf=new PDF();
include('db.php');
$carrera=substr($pensum, 0, 1);


$pensum = $pdf->pensum($carrera);
$carrera_a1 = $pdf->carrera_larga($carrera);
$carrera_a2 = $pdf->carrera_corta($carrera);
$pdf->encabezado("ESTADÍSTICA DE LA SECCIONES DE ".$carrera_a2." ".substr($lapso, 0, 4));	


$seccion=$primera_seccion."0";
$R=50;
$L=20; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);		
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."1";
$R=50;
$L=55; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."2";
$R=50;
$L=90; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."3";
$R=50;
$L=125; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."4";
$R=50;
$L=160; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."5";
$R=80;
$L=20; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."6";
$R=80;
$L=55; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);		
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."7";
$R=80;
$L=90; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."8";
$R=80;
$L=125; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;

$seccion=$primera_seccion."9";
$R=80;
$L=160; 
$dat=$pdf->verificar_secciones_por_carrera($L,$R,$total_carrera,$X1B,$carrera_a2,$carrera,$seccion);
list($sumatoria,$cantidad) = split('[|]', $dat);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($L,$R+20);	
$pdf->Cell(136, 5, "Total : ".$sumatoria, 0, 0, 'L', 0);
if ($sumatoria>0) {
	$cantidad_secciones=$cantidad_secciones+1;
}
$total=$total+$sumatoria;



$pdf->SetFont('Arial','B',12);
$pdf->SetXY(15,115);	
$pdf->Cell(180, 5, "Cantidad de secciones: ".$cantidad_secciones, 0, 0, 'C', 0);


$pdf->SetXY(15,120);	
$pdf->Cell(180, 5, "Total de alumno de la especialidad: ".$total, 0, 0, 'C', 0);


$pdf->Output();
?>
