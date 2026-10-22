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


include('/Classes/class_api.php');

$pdf=new PDF('L','mm','A4');
$X0_3=1;

$carrera= substr($_POST["pensum"], 0, 1);       
$carrera_a1=$pdf->carrera_larga($carrera);
$carrera_a2=$pdf->carrera_corta($carrera);

$pdf->alumno_telefonos_Encabezado($X0_3,$carrera_a1);

include('db.php');    
$sql = "SELECT * FROM alumno WHERE carrera='".$carrera."' and actividad=1 ORDER BY nombre ASC";// 
$resultado = $conn->query($sql);

$i=1;
$i2=1;
$X1=-10;
$X0_2=0;
$X0=0;

if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$pdf->SetFont('Arial','',7);  
		$pdf->SetXY(5,65+$X1);
		$pdf->Cell(8, 5, $i, 1, 1, 'L', 0);

		$pdf->SetXY(13,65+$X1);
		$pdf->Cell(15, 5, $fila["cedula"], 1, 1, 'L', 0);

		$pdf->SetXY(28,65+$X1);
		$pdf->Cell(70, 5, utf8_decode(strtoupper($fila["nombre"])), 1, 1, 'L', 0);

		$pdf->SetXY(98,65+$X1);
		$pdf->Cell(20, 5, utf8_decode(strtoupper($fila["telefonoh"])), 1, 1, 'L', 0);

		$pdf->SetXY(118,65+$X1);
		$pdf->Cell(20, 5, utf8_decode(strtoupper($fila["telefonoc"])), 1, 1, 'L', 0);

		$pdf->SetXY(138,65+$X1);
		$pdf->Cell(20, 5, utf8_decode(strtoupper($fila["telefonot"])), 1, 1, 'L', 0);

		$pdf->SetXY(158,65+$X1);
		$pdf->Cell(60, 5, utf8_decode(strtoupper($fila["email"])), 1, 1, 'L', 0);

		$pdf->SetXY(218,65+$X1);
		$pdf->Cell(75, 5, utf8_decode(strtoupper($fila["direccion"])), 1, 1, 'L', 0);

		$X1=$X1+5;

		$X0=$X0+1;
		$X0_2=$X0_2+1;
		$X1B=$X1B+5;

		$i++; 
		$i2++;

		if($X0_2>22){
			$X0_3=$X0_3+1;

			$pdf->alumno_telefonos_Encabezado($X0_3,$carrera_a1);
			$X1=-10;
			$X0_2=0;

		}

	}
}


$pdf->Output();


?>
