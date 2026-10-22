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



require ('num2letras.php');

include('/Classes/class_api.php');
$pdf=new PDF();
$X0_3=1;

$carrera=substr($_POST["pensum"], 0, 1);	 
$cedula=$_POST["cedula"];
$lapso=$_POST["lapso"];

$cedula="V24918141";
$lapso="2020-1";

$pensum = $pdf->pensum($carrera);
$carrera_a1 = $pdf->carrera_larga($carrera);
$carrera_a2 = $pdf->carrera_corta($carrera);


// echo $_POST["pensum"]."<br>";
// echo $seccion."<br>";
// echo $lapso."<br>";
// echo $trayecto."<br>";


$pdf->AddPage();
date_default_timezone_set('America/Caracas');
$fechaActual = date('d-m-Y');   
$DIA=date("d");
$MES=date("m");
$AÑO=date("Y");

date_default_timezone_set('UTC');
$hoy = date("d-m-Y");

			        //$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','B',12);


$X1=5;
$pdf->Image("LOGO.jpg" , 10 ,5+$X1, 43 , 25 , "jpg" ,"");

$pdf->SetXY(54,0+$X1);
$pdf->Cell(136, 5, utf8_decode("REPÚBLICA BOLIVARIANA DE VENEZUELA"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','',10);
$pdf->SetXY(54,4+$X1);
$pdf->Cell(136, 5, utf8_decode("MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA"), 0, 0, 'L', 0);
$pdf->SetXY(54,8+$X1);
$pdf->Cell(136, 5, utf8_decode("CIENCIA Y TECNOLOGÍA"), 0, 0, 'L', 0);

$pdf->SetXY(54,15+$X1);
$pdf->Cell(136, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'L', 0);
$pdf->SetXY(54,19+$X1);
$pdf->Cell(136, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'L', 0);
$pdf->SetXY(54,23+$X1);
$pdf->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',10);
$pdf->SetXY(55,33+$X1);
$pdf->Cell(100, 7,"LISTADO DE MATERIAS INSCRITAS ".utf8_decode($carrera_a1)." ".substr($lapso, 0, 4), 0, 0, 'C', 0);

$pdf->SetFont('Arial','B',7);
$pdf->SetXY(15,39+$X1);
$pdf->Cell(100, 7,"SECCION: ".$trayecto." - ".$SECCION, 0, 0, 'L', 0);

$X1=-15;
$pdf->SetFont('Arial','B',7);

$pdf->SetXY(15,65+$X1);
$pdf->Cell(180, 5, "", 1, 1, 'L', 0);

$pdf->SetXY(15,65+$X1);
$pdf->Cell(136, 5, utf8_decode("N°"), 0, 0, 'L', 0);

$pdf->SetXY(23,65+$X1);
$pdf->Cell(136, 5, utf8_decode("COD_MAT"), 0, 0, 'L', 0);

$pdf->SetXY(45,65+$X1);
$pdf->Cell(136, 5, utf8_decode("MATERIA"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,270);
$pdf->Cell(170, 5, utf8_decode("Página: ").$X0_3, 0, 0, 'C', 0);


include('db.php');  


$sql = "SELECT DISTINCT lismat.semestre,lismat.trayecto,lismat.descrip2,notas.id,notas.codigo,notas.nota,notas.lapso,notas.tiplap,notas.cod_mat,notas.seccion,notas.acu,notas.cod_doc FROM notas,lismat WHERE notas.cod_mat = lismat.cod_mat and notas.codigo='".$cedula."' and notas.lapso='".$lapso."' ORDER BY lismat.id ASC";
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
		$pdf->Cell(136, 5, $fila["cod_mat"], 0, 0, 'L', 0);

		$pdf->SetXY(45,65+$X1);
		$pdf->Cell(136, 5, utf8_decode(strtoupper($fila["descrip2"])), 0, 0, 'L', 0);

	    $X1=$X1+5;

		$X0=$X0+1;
		$X0_2=$X0_2+1;
		$X1B=$X1B+5;

		$i++; 
		$i2++;


		if($X0_2>42){
			$X0_3=$X0_3+1;

			$pdf->listado_seccion_Encabezado($X0_3,$carrera_a1,$carrera,$seccion,$lapso,$trayecto);
			$X1=-10;
			$X0_2=0;
		}

	}
}


$pdf->Output();


?>
