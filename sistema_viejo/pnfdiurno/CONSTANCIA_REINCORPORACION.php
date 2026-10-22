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


require 'fpdf.php';
require ('num2letras.php');


$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);



date_default_timezone_set('America/Caracas');
$fechaActual = date('d-m-Y');   
$d=date("d");
$m=date("m");
$y=date("Y");


$DIA = strtolower(num2letras($d));
$AÑO = strtolower(num2letras($y));
$MES=ucfirst(nombremes($m));

function nombremes($mes1)
{
	setlocale(LC_TIME, 'spanish');
	$mes = strftime("%B", mktime(0, 0, 0, $mes1, 1, 2000));
	return $mes;
}

function lugares($valor1)
{
	
	switch ($valor1) {

		case 1:
		$valor = "PRIMER";
		break;
		case 2:
		$valor = "SEGUNDO";
		break;
		case 3:
		$valor = "TERCER";
		break;
		case 4:
		$valor = "CUARTO";
		break;
		case 5:
		$valor = "CINCO";
		break;	
		case 6:
		$valor = "SESTO";
		break;
		case 7:
		$valor = "SÉPTIMO";
		break;
		case 8:
		$valor = "OCTAVO";
		break;
		case 9:
		$valor = "NOVENO";
		break;
		case 10:
		$valor = "DECIMO";
		break;
		case 11:
		$valor = "ONCEAVO";
		break;
		case 12:
		$valor = "DOCEAVO";
		break;

		default:
		$valor =  "";
		break;
	}
	return $valor;
}
 
$X1=5;

$pdf->Image("LOGO1.jpg" , 17 ,0+$X1, 35 , 25 , "jpg" ,"");
$pdf->Image("LOGO2.jpg" , 155 ,2+$X1, 35 , 25 , "jpg" ,"");

$pdf->SetFont('Arial','',5);
$pdf->SetXY(17,25+$X1);
$pdf->Cell(180, 5, "Nº de control: 00000000007100939", 0, 0, 'L', 0);

$X1=10;
$pdf->SetFont('Arial','',13);

$pdf->SetXY(0,0+$X1);
$pdf->Cell(210, 5, utf8_decode("República Bolivariana de Venezuela"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',10);
$pdf->SetXY(0,4+$X1);
$pdf->Cell(210, 5, utf8_decode("Ministerio del Poder Popular,"), 0, 0, 'C', 0);
$pdf->SetXY(0,8+$X1);
$pdf->Cell(210, 5, utf8_decode("Para la Educación Universitaria"), 0, 0, 'C', 0);

$pdf->SetXY(0,13+$X1);
$pdf->Cell(210, 5, utf8_decode("Ciencia y Tecnología"), 0, 0, 'C', 0);


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(55,35+$X1);
$pdf->Cell(100, 10, utf8_decode("CONSTANCIA DE REINCORPORACION"), 1, 1, 'C', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(45,55+$X1);
$pdf->Cell(180, 5, utf8_decode("QUIEN    SUSCRIBE,   HACE   CONSTAR   QUE    EL (LA)   CIUDADANO (A):"), 0, 0, 'L', 0);



$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}

$id=15;

include "db.php";

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {                      
		$cedula = $fila["cedula"];
		$nombre  = utf8_decode($fila['nombre']);
		$carrera = $fila['carrera'];
		$codigo = $fila['codigo'];			
	}  
}		

$pdf->SetXY(30,65+$X1);
$pdf->Cell(180, 5, $nombre, 0, 0, 'L', 0);

$pdf->SetXY(136,65+$X1);
$pdf->Cell(180, 5, utf8_decode("TITULAR DE LA CÉDULA"), 0, 0, 'L', 0);

$pdf->Line(20, 70+$X1, 134, 70+$X1);


$pdf->SetXY(20,75+$X1);
$pdf->Cell(180, 5, utf8_decode("NÚMERO"), 0, 0, 'L', 0);

$pdf->SetXY(40,75+$X1);
$pdf->Cell(180, 5, $cedula, 0, 0, 'L', 0);
$pdf->Line(40, 80+$X1, 63, 80+$X1);

$pdf->SetXY(63,75+$X1);
$pdf->Cell(180, 5, utf8_decode("CURSANTE DEL "), 0, 0, 'L', 0);

$pdf->SetXY(98,75+$X1);
$pdf->Cell(20, 5, lugares(4), 0, 0, 'C', 0);

$pdf->Line(96, 80+$X1, 119, 80+$X1);

$pdf->SetXY(121,75+$X1);
$pdf->Cell(180, 5, utf8_decode("SEMESTRE   EN   LA   CARRERA"), 0, 0, 'L', 0);


include('getpensum3_clase.php');
$con = new carreras();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);

$pdf->SetFont('Arial','B',14);
$pdf->SetXY(55,85+$X1);
$pdf->Cell(100, 10, $Carrera_a1, 1, 1, 'C', 0);




//and id= (SELECT COUNT(id) FROM `lapso`)

$sql = "SELECT * FROM `lapso` WHERE carrera= '".$carrera."' ORDER BY `lapso` ASC";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {  
		$nombre_lapso= $fila['descrip'];
		$lapso_actual= $fila['lapso'];

	}  
}


$pdf->SetFont('Arial','',11);
$pdf->SetXY(0,100+$X1);
$pdf->Cell(210, 5, utf8_decode("SOLICITÓ SU REINCORPORACION DURANTE EL LAPSO"), 0, 0, 'C', 0);

$pdf->SetXY(80,110+$X1);
$pdf->Cell(50, 10, $lapso_actual, 1, 1, 'C', 0);


$pdf->SetFont('Arial','B',11);
$pdf->SetXY(17,142+$X1);
$pdf->Cell(180, 5, utf8_decode("IMPORTANTE"), 0, 0, 'C', 0);
$pdf->SetFont('Arial','',11);

$pdf->SetXY(17,150+$X1);
$pdf->Cell(180, 5, utf8_decode("DEBE RESENTARSE EL DIA DE SU INSCRIPCION POR SU DEPARTAMENTO"), 0, 0, 'L', 0);
$pdf->SetXY(17,155+$X1);
$pdf->Cell(180, 5, utf8_decode("Y SOLICITAR SU REPORTE INSCRIPCION Y SU HISTORIAL ACADEMICO"), 0, 0, 'L', 0);

$pdf->SetXY(17,138+$X1);
$pdf->Cell(170, 25, "", 1, 1, 'C', 0);


$pdf->SetXY(17,180+$X1);
$pdf->Cell(180, 5, utf8_decode("CONSTANCIA QUE SE EXPIDE EN PTO. CABELLO, A LOS  ").strtoupper(num2letras($d)), 0, 0, 'L', 0);

$pdf->SetXY(17,190+$X1);
$pdf->Cell(180, 5, utf8_decode("DIAS DEL MES DE "), 0, 0, 'L', 0);

$pdf->SetXY(53,190+$X1);
$pdf->Cell(30, 5,strtoupper($MES), 0, 0, 'C', 0);
$pdf->Line(54, 195+$X1, 80, 195+$X1);

$pdf->SetXY(82,190+$X1);
$pdf->Cell(180, 5," DEL ".strtoupper($AÑO), 0, 0, 'L', 0);
 


$pdf->Line(82, 185+$X1, 111, 185+$X1);
$pdf->Line(125, 185+$X1, 155, 185+$X1);





$X1=30;

$R1=3;


$query = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$usuario = $row['nombre'];
}  		

$pdf->SetXY(37,185+$X1);
$pdf->Cell(180, 5, utf8_decode("Emitido por:"), 0, 0, 'L', 0);



$pdf->Line(35+$R1, 204+$X1-5, 90+$R1, 204+$X1-5);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(32+$R1,200+$X1);
$pdf->Cell(60, 5, utf8_decode($usuario), 0, 0, 'C', 0);
$pdf->SetFont('Arial','',10);
$pdf->SetXY(49+$R1,205+$X1);
$pdf->Cell(180, 5, utf8_decode("FUNCIONARIO"), 0, 0, 'L', 0);




$R=90;


$pdf->SetXY(115,185+$X1);
$pdf->Cell(180, 5, utf8_decode("Revisado Por:"), 0, 0, 'L', 0);


$pdf->Line(117, 204+$X1-5, 172, 204+$X1-5);
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(34+$R,200+$X1);
$pdf->Cell(180, 5, utf8_decode("MSc. JUSTIBER IBARRA"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','',10);
$pdf->SetXY(33+$R,205+$X1);
$pdf->Cell(180, 5, utf8_decode("Jefa de Control de Estudios"), 0, 0, 'L', 0);

$pdf->SetXY(35+$R,210+$X1);
$pdf->Cell(180, 5, utf8_decode("del I.U.T. Puerto Cabello"), 0, 0, 'L', 0);












$pdf->Output();



?>
