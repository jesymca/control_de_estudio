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

$pensum=$_POST["pensum"];
$cedula=$_POST["cedula"];
$grado=$_POST["grado"];


include('/Classes/class_api.php');
$pdf=new PDF();

// $cantidad_materias=$pdf->cantidad_materias($pensum,$grado);
// $materias_aprobadas=$pdf->materias_aprobadas($pensum,$cedula,$grado);
// if($materias_aprobadas==$cantidad_materias AND $grado=="T" OR $materias_aprobadas==$cantidad_materias AND $grado=="I" OR $materias_aprobadas==$cantidad_materias AND $grado=="L"){

$pdf->AddPage();

date_default_timezone_set('America/Caracas');
$fechaActual = date('d-m-Y');   
$d=date("d");
$m=date("m");
$y=date("Y");
$MES = $pdf->nombremes($m);
$DIA = $pdf->num2letras($d);
$AÑO = $pdf->num2letras($y);


$X1=5;
$pdf->Image("LOGO.jpg" , 5 ,3, 53 , 35 , "jpg" ,"");
$pdf->SetFont('Arial','',13);
$pdf->SetXY(54,0+$X1);
$pdf->Cell(136, 5, utf8_decode("REPÚBLICA BOLIVARIANA DE VENEZUELA"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',10);
$pdf->SetXY(54,4+$X1);
$pdf->Cell(136, 5, utf8_decode("MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA,"), 0, 0, 'L', 0);
$pdf->SetXY(54,8+$X1);
$pdf->Cell(136, 5, utf8_decode("CIENCIA Y TECNOLOGÍA"), 0, 0, 'L', 0);

$pdf->SetXY(54,13+$X1);
$pdf->Cell(136, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'L', 0);

include "db.php";

$sql = "SELECT * FROM sede where id=1";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {                      
		$sede  = $fila['lugar'];					
	}  
}

$pdf->SetXY(54,17+$X1);
$pdf->Cell(136, 5, utf8_decode($sede), 0, 0, 'L', 0);
$pdf->SetXY(54,21+$X1);
$pdf->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',14);
$pdf->SetXY(17,35+$X1);
$pdf->Cell(180, 5, utf8_decode("CERTIFICACION DE TRAMITACION DE TITULO"), 0, 0, 'C', 0);


include('db.php');  

$sql = "SELECT * FROM directivos where cargo='Director'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
		$jefe_nombre=$fila["nombre"];
		$jefe_cedula=$fila["cedula"];
		$jefe_cargo=$fila["cargo"];
		$jefe_lugar=$fila["lugar"];

	}
}


$pdf->SetFont('Arial','',11);
$pdf->SetXY(30,50+$X1);
$pdf->Cell(180, 5, utf8_decode("Quien   Subscribe,   "), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(65,50+$X1);
$pdf->Cell(180, 5, $jefe_nombre, 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(125,50+$X1);
$pdf->Cell(180, 5, utf8_decode(",  titular    de    la    cédula    de"), 0, 0, 'L', 0);


$pdf->SetFont('Arial','',11);
$pdf->SetXY(30,60+$X1);
$pdf->Cell(180, 5, utf8_decode("identidad"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(47,60+$X1);
$pdf->Cell(180, 5, utf8_decode("Nº ").$jefe_cedula, 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(77,60+$X1);
$pdf->Cell(180, 5,utf8_decode(", Director de la Universidad Politécnica Territorial de Puerto"), 0, 0, 'L', 0);


$pdf->SetXY(30,70+$X1);
$pdf->Cell(180, 5, utf8_decode("Cabello, según Resolución Nº 072 de fecha veinte (20) de julio  de  dos  mil  dieciocho "), 0, 0, 'L', 0);

$pdf->SetXY(30,80+$X1);
$pdf->Cell(180, 5, utf8_decode("2018,  Gaceta  Oficial de la República Bolivariana de Venezuela  Nº 41.450  de  fecha"), 0, 0, 'L', 0);

$pdf->SetXY(30,90+$X1);
$pdf->Cell(180, 5, utf8_decode("treinta  y  uno  (31)  de  julio  de  dos  mil  dieciocho  (2018),  "), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(135,90+$X1);
$pdf->Cell(180, 5, utf8_decode("CERTIFICO"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(158,90+$X1);
$pdf->Cell(180, 5, utf8_decode("que  el  (la)"), 0, 0, 'L', 0);


include "db.php";

$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {		
		$nombre  = utf8_decode($fila['nombre']);
		$carrera  = utf8_decode($fila['carrera']);		
	}  
}

$pdf->SetXY(30,100+$X1);
$pdf->Cell(180, 5, utf8_decode("ciudadano  (a):   "), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(60,100+$X1);
$pdf->Cell(180, 5, utf8_decode($nombre).",", 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(133,100+$X1);
$pdf->Cell(180, 5, utf8_decode("titular   de   la  cédula   de"), 0, 0, 'L', 0);


$carrera_a1 = $pdf->carrera_larga($carrera);
$carrera_a2 = $pdf->carrera_corta($carrera);

if($grado=="T"){

$pdf->SetXY(30,110+$X1);
$pdf->Cell(180, 5, utf8_decode("identidad  Nº  "), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(55,110+$X1);
$pdf->Cell(180, 5, $cedula, 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(75,110+$X1);
$pdf->Cell(180, 5, utf8_decode(", cumplió  con  los  requisitos  de  egreso  para  ser Técnico"), 0, 0, 'L', 0);


$pdf->SetXY(30,120+$X1);
$pdf->Cell(180, 5, utf8_decode("Superior Universitario en : "), 0, 0, 'L', 0);
}

if($grado=="I"){
$pdf->SetXY(30,110+$X1);
$pdf->Cell(180, 5, utf8_decode("identidad Nº ").$cedula.utf8_decode(", cumplió con los requisitos de egreso para ser Ingeniero en :"), 0, 0, 'L', 0);
}

if($grado=="L"){
$pdf->SetXY(30,110+$X1);
$pdf->Cell(180, 5, utf8_decode("identidad Nº ").$cedula.utf8_decode(", cumplió con los requisitos de egreso para ser Licenciado"), 0, 0, 'L', 0);
$pdf->SetXY(30,120+$X1);
$pdf->Cell(180, 5, utf8_decode("en : "), 0, 0, 'L', 0);
}

$pdf->SetFont('Arial','B',12);
$pdf->SetXY(17,131+$X1);
$pdf->Cell(180, 5,$carrera_a1, 0, 0, 'C', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(17,140+$X1);
$pdf->Cell(180, 5, utf8_decode("Ha solicitado y cancelado los derechos de expedición del título."), 0, 0, 'C', 0);

$pdf->SetXY(17,155+$X1);
$pdf->Cell(180, 5, utf8_decode("Y, para que surta los mismos efectos del Título, expido esta certificación a"), 0, 0, 'C', 0);

$pdf->SetXY(17,165+$X1);
$pdf->Cell(180, 5, utf8_decode("petición de la persona interesada y con carácter provisional hasta que el título se edite."), 0, 0, 'C', 0);

$pdf->SetFont('Arial','B',11); 
$pdf->Line(75+$R2, 199+$X1, 137+$R2, 199+$X1);

$pdf->SetXY(17+$R,200+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_nombre), 0, 0, 'C', 0);
$pdf->SetFont('Arial','',11); 
$pdf->SetXY(17+$R,205+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_cargo), 0, 0, 'C', 0);

$pdf->SetXY(17+$R,210+$X1);
$pdf->Cell(180, 5, utf8_decode("Universidad Politécnica Territorial de Puerto Cabello"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',9);
$pdf->SetXY(17+$R,215+$X1);
$pdf->Cell(180, 5, utf8_decode("Dirección de correo electrónico: uptpuertocabello@gmail.com"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',9); 
$pdf->SetXY(17,254+$X1);
$pdf->Cell(180, 5, utf8_decode("TRANSFORMACIÓN UNIVERSITARIA CON CALIDAD, PERTINENCIA Y PRODUCTIVIDAD")."...", 0, 0, 'C', 0);

$pdf->SetLineWidth(1);

$pdf->SetDrawColor(255, 255, 0);
$pdf->SetFillColor(255, 255, 0);   
$pdf->Line(30, 264, 180, 264);

$pdf->SetDrawColor(0, 0, 255);
$pdf->SetFillColor(0, 0, 255);    
$pdf->Line(30, 265, 180, 265);

$pdf->SetDrawColor(255, 0, 0);
$pdf->SetFillColor(255, 0, 0);   
$pdf->Line(30, 266, 180, 266);

$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','',8); 
$pdf->SetXY(17,263+$X1);
$pdf->Cell(180, 5, utf8_decode("Urbanización La Elvira, Zona Industrial Santa Rosa, Galpón Nº08, Puerto Cabello."), 0, 0, 'C', 0);

$pdf->SetXY(17,266+$X1);
$pdf->Cell(180, 5, utf8_decode("Teléfonos: (0242)3720094. controldeestudios.iutpc@gmail.com"), 0, 0, 'C', 0);

$pdf->Output();

// }else{
// 	header("Location: msg_no_graduado.php");
// }

?>
