<?php

$grado = $_POST['grado'];
$lapso = $_POST['lapso'];

include('/Classes/class_api.php');
$pdf=new PDF();
include('db.php');

$pdf->encabezado("ESTADÍSTICA POR ESPECIALIDAD"." ".substr($lapso, 0, 4));	
$sql = "SELECT * FROM pensum";
$resultado = $conn->query($sql);
$R=5;
$L=5;

$pdf->SetFont('Arial','B',10);
$pdf->SetXY(15+$L,55);		
$pdf->Cell(80, 10, "ESPECIALIDAD", 1, 1, 'L', 0);
$pdf->SetXY(95+$L,55);		
$pdf->Cell(30, 10, "SECCIONES", 1, 1, 'C', 0);
$pdf->SetXY(125+$L,55);		
$pdf->Cell(30, 10, "ALUMNOS", 1, 1, 'C', 0);
$pdf->SetXY(155+$L,55);		
$pdf->Cell(30, 10, "DOCENTES", 1, 1, 'C', 0);

$pdf->SetFont('Arial','',10);
$R=10;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) { 
		$descripcion2=$fila["descripcion2"];
		$pensum=$fila["pensum"];

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

		$dat=$pdf->verificar_secciones_por_carrera_3($pensum,$L,$R,$seccion,$lapso);
		list($carrera_a2,$cantidad_secciones,$total) = split('[|]', $dat);
	    $docentes=$pdf->cantida_de_docente($pensum,$lapso);

        
        $pdf->SetXY(15+$L,55+$R);		
        $pdf->Cell(80, 20, utf8_decode($carrera_a2), 1, 1, 'L', 0);
        $pdf->SetXY(95+$L,55+$R);		
        $pdf->Cell(30, 20, $cantidad_secciones, 1, 1, 'C', 0);
        $pdf->SetXY(125+$L,55+$R);		
        $pdf->Cell(30, 20, $total, 1, 1, 'C', 0);
        
        if($docentes==""){
			$docentes="SIN DOCENTE";
		}
        $pdf->SetXY(155+$L,55+$R);		
        $pdf->Cell(30, 20, $docentes, 1, 1, 'C', 0);
		
        $total_total=$total_total+$total;
        $R=$R+20;
	}
}


// $pdf->SetXY(79,55+$R);		
// $pdf->Cell(50, 20, "TOTAL DE ALUMNOS INSCRIPTOS: ", 0, 0, 'L', 0);
$pdf->SetLineWidth(0.6);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(125+$L,55+$R);		
$pdf->Cell(30, 10, "TOTAL", 1, 1, 'C', 0);
$pdf->SetXY(155+$L,55+$R);		
$pdf->Cell(30, 10, "TOTAL", 1, 1, 'C', 0);
$R=$R+10;
$pdf->SetFont('Arial','',10);	
$pdf->SetXY(130,55+$R);		
$pdf->Cell(30, 20, $total_total, 1, 1, 'C', 0);



$cantida_total_docente=$pdf->cantida_de_docente_total($lapso);
$pdf->SetXY(160,55+$R);		
$pdf->Cell(30, 20, $cantida_total_docente, 1, 1, 'C', 0);

$pdf->Output();

?>
