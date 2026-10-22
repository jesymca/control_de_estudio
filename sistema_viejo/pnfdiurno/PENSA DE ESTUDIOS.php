<?php

//require ("aud.php");
//auditar("PENSA DE ESTUDIO",$_POST["SECCION"]." - ".$_POST["LAPSO"]);


include('/Classes/class_api.php');
$pdf=new PDF();
$pensum=$_POST["pensum"];
$pdf->Encabezado_pensa($pensum);


include "db.php";

$sql = "SELECT * FROM `lismat` WHERE `pensum`='".$pensum."' and SUBSTRING(`cod_mat`,2,1)='R' and SUBSTRING(`cod_mat`,2,1)<>'P' and SUBSTRING(`cod_mat`,2,1)<>'T' and SUBSTRING(`cod_mat`,2,1)<>'E'";
$resultado =$conn->query($sql);

$X1B=5;
$X0=0;
$t=0;

$CREDSUM=0;
if($pensum=="AXC" OR $pensum=="RXC"){
	$tramo="Lapso";
}else{
	$tramo="Trayecto";
}
while($fila = $resultado->fetch_assoc()) { 

    $leng=strlen($fila['cod_mat']);

	$COD_MAT  = $fila['cod_mat'];
	$DESCRIP2  = substr(utf8_decode($fila['descrip2']), 0, 52);
	$CRED  = $fila['creditos'];
	$CREDSUM=$CREDSUM+$fila['creditos'];

	if($fila['trayecto']==0 AND $t==0){			
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetXY(15,42+$X1B);
		$pdf->Cell(180, 4, $tramo.":  0", 0, 0, 'L', 0);
		$t=1;			
		$X1B=$X1B+5;
	}

	if($fila['trayecto']==1 AND $t==1){	
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetXY(15,42+$X1B);
		$pdf->Cell(180, 4, $tramo.":  1", 0, 0, 'L', 0);
		$t=2;			
		$X1B=$X1B+5;
	}

	if($fila['trayecto']==2 AND $t==2){				
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetXY(15,42+$X1B);
		$pdf->Cell(180, 4, $tramo.":  2", 0, 0, 'L', 0);
		$t=3;			
		$X1B=$X1B+5;
	}

	if($fila['trayecto']==3 AND $t==3){				
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetXY(15,42+$X1B);
		$pdf->Cell(180, 4, $tramo.":  3", 0, 0, 'L', 0);
		$t=4;			
		$X1B=$X1B+5;
	}

	if($fila['trayecto']==4 AND $t==4){			
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetXY(15,42+$X1B);
		$pdf->Cell(180, 4, $tramo.":  4", 0, 0, 'L', 0);
		$t=5;			
		$X1B=$X1B+5;
	}

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(15,42+$X1B);
	$pdf->Cell(15, 5, $COD_MAT, 1, 1, 'C', 0);

	$pdf->SetXY(30,42+$X1B);
	$pdf->Cell(92, 5, $DESCRIP2, 1, 1, 'L', 0);

	$pdf->SetXY(122,42+$X1B);
	$pdf->Cell(7, 5, $CRED, 1, 1, 'C', 0);

	$pdf->SetXY(129,42+$X1B);
	$pdf->Cell(14, 5, $fila['pre1'], 1, 1, 'C', 0);
	$pdf->SetXY(143,42+$X1B);
	$pdf->Cell(14, 5, $fila['pre2'], 1, 1, 'C', 0);
	$pdf->SetXY(157,42+$X1B);
	$pdf->Cell(14, 5, $fila['pre3'], 1, 1, 'C', 0);
	$pdf->SetXY(171,42+$X1B);
	$pdf->Cell(14, 5, $fila['pre4'], 1, 1, 'C', 0);
	$pdf->SetXY(185,42+$X1B);
	$pdf->Cell(9, 5, $fila['aprobatori'], 1, 1, 'C', 0);

	$X1B=$X1B+5;
	$X0=$X0+1;

	if($X0>39){
		$pdf->Encabezado_pensa($pensum);
		$X1B=5;	
		$X0=0;
	}

}


$pdf->SetFont('Arial','',10);
$pdf->SetXY(15,49+$X1B);
$pdf->Cell(185.5, 5, "TOTAL CREDITOS DEL SEMESTRE:", 0, 0, 'C', 0);
$pdf->SetXY(15,56+$X1B);
$pdf->Cell(185.5, 5, "TOTAL CREDITOS DE LA CARRERA: ".$CREDSUM, 0, 0, 'C', 0);

$pdf->Output();


?>
