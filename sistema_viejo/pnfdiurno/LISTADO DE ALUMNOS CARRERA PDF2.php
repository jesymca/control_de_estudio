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

$pdf=new PDF();
$X0_3=1;

$carrera= substr($_POST["pensum"], 0, 1);       
$lapso= $_POST["lapso"]; 

$carrera_a1=$pdf->carrera_larga($carrera);
$carrera_a2=$pdf->carrera_corta($carrera);

$pdf->alumno_carrera_Encabezado2($X0_3,$carrera_a1,$lapso);

include('db.php');

$sql = "SELECT DISTINCT alumno.cedula,alumno.nombre,alumno.actividad,notas.lapso FROM alumno,notas WHERE notas.carrera='".$carrera."' and notas.lapso='".$lapso."' and notas.codigo=alumno.cedula ORDER BY nombre ASC";// 
$resultado = $conn->query($sql);

$i=1;
$i2=1;
$X1=-10;
$X0_2=0;
$X0=0;

if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$pdf->SetFont('Arial','',7);  
		$pdf->SetXY(15,65+$X1);
		$pdf->Cell(136, 5, $i, 0, 0, 'L', 0);

		$pdf->SetXY(23,65+$X1);
		$pdf->Cell(136, 5, $fila["cedula"], 0, 0, 'L', 0);

		$pdf->SetXY(45,65+$X1);
		$pdf->Cell(136, 5, utf8_decode(strtoupper($fila["nombre"])), 0, 0, 'L', 0);

		$pdf->SetXY(130,65+$X1);
		$pdf->Cell(136, 5, utf8_decode($carrera_a2), 0, 0, 'L', 0);

		$pdf->SetXY(165,65+$X1);
		$pdf->Cell(136, 5, $fila["actividad"], 0, 0, 'L', 0);
		$X1=$X1+5;

		$X0=$X0+1;
		$X0_2=$X0_2+1;
		$X1B=$X1B+5;

		$i++; 
		$i2++;

		if($X0_2>42){
			$X0_3=$X0_3+1;

			$pdf->alumno_carrera_Encabezado($X0_3,$carrera_a1);
			$X1=-10;
			$X0_2=0;

		}

	}
}


$pdf->Output();


?>
