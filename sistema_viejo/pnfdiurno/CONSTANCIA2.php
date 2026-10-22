<?php


$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}


include "db.php";

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {                      
		$cedula = $fila["cedula"];
		$nombre  = utf8_decode($fila['nombre']);
		$carrera = $fila['carrera'];
		$codigo = $fila['codigo'];	
		$actividad = $fila['actividad'];		
	}  
}		

if ($actividad==1) {

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

function nombremes($mes1)
{
	setlocale(LC_TIME, 'spanish');
	$mes = strftime("%B", mktime(0, 0, 0, $mes1, 1, 2000));
	return $mes;
}

$DIA = strtolower(num2letras($d));
$AÑO = strtolower(num2letras($y));
$MES=ucfirst(nombremes($m));



 
$X1=5;

$pdf->Image("LOGO.jpg" , 15 ,0+$X1, 43 , 25 , "jpg" ,"");
$pdf->Image("LOGO2.jpg" , 155 ,2+$X1, 35 , 25 , "jpg" ,"");

$pdf->SetFont('Arial','',5);
$pdf->SetXY(17,25+$X1);
$pdf->Cell(180, 5, utf8_decode("Nº de control: 00000000007100939"), 0, 0, 'L', 0);

$X1=10;
$pdf->SetFont('Arial','',13);

$pdf->SetXY(20,0+$X1);
$pdf->Cell(180, 5, utf8_decode("República Bolivariana de Venezuela"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',10);
$pdf->SetXY(20,4+$X1);
$pdf->Cell(180, 5, utf8_decode("Ministerio del Poder Popular,"), 0, 0, 'C', 0);
$pdf->SetXY(20,8+$X1);
$pdf->Cell(180, 5, utf8_decode("Para la Educación Universitaria"), 0, 0, 'C', 0);

$pdf->SetXY(20,13+$X1);
$pdf->Cell(180, 5, utf8_decode("Ciencia y Tecnología"), 0, 0, 'C', 0);


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(75,35+$X1);
$pdf->Cell(70, 5, utf8_decode("CONSTANCIA DE ESTUDIOS"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(20,55+$X1);
$pdf->Cell(180, 5, utf8_decode("Quien suscribe Jefe del Departamento de Control de Estudio de nuestra Institución, Hace Constar"), 0, 0, 'L', 0);

$pdf->SetXY(20,65+$X1);
$pdf->Cell(180, 5, utf8_decode("que el (la) Ciudadano (a) que se menciona a continuación"), 0, 0, 'L', 0);




include('getpensum3_clase.php');
$con = new carreras();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);


$sql = "SELECT * FROM `lapso` WHERE carrera= '".$carrera."' ORDER BY `ID` ASC";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {
		$nombre_lapso= $fila['descrip'];
		$lapso_actual= $fila['lapso'];
	}  
}


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(20,80+$X1);
$pdf->Cell(180, 5, $nombre, 0, 0, 'C', 0);

$pdf->SetFont('Arial','',11);


$pdf->SetXY(17,95+$X1);
$pdf->Cell(180, 5, utf8_decode("Titular de la Cédula de Identidad "), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(75,95+$X1);
$pdf->Cell(180, 5, utf8_decode("Nº (").$cedula.")", 0, 0, 'L', 0);
$pdf->SetFont('Arial','',11);
$pdf->SetXY(103,95+$X1);
$pdf->Cell(180, 5, utf8_decode(", se encuentra inscrito en esta casa de"), 0, 0, 'L', 0);


$pdf->SetXY(17,105+$X1);
$pdf->Cell(180, 5, utf8_decode("estudio y es cursante del Programa Nacional de Formación en:"), 0, 0, 'L', 0);


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(17,120+$X1);
$pdf->Cell(180, 5, $carrera_a1, 0, 0, 'C', 0);


$pdf->SetFont('Arial','',11);
$pdf->SetXY(17,135+$X1);
$pdf->Cell(180, 5, utf8_decode("Cuyo lapso académico ").substr($lapso_actual, 0, 4).utf8_decode(" (Trimestre) y su vigencia corresponde desde"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);

$pdf->SetFont('Arial','B',14);
$pdf->SetXY(17,150+$X1);
$pdf->Cell(180, 5, utf8_decode($nombre_lapso), 0, 0, 'C', 0);


$pdf->SetFont('Arial','',11);

include "db.php";

$sql = "SELECT * FROM sede where id=2";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {                      
		$sede  = $fila['lugar'];					
	}  
}	



if($d=="01"){
$pdf->SetXY(17,170+$X1);
$pdf->Cell(180, 5, utf8_decode("Documento que se emite en la Ciudad de ").$sede.", al primer "."(".$d.")".utf8_decode(" día del mes de"), 0, 0, 'L', 0);
}else{
$pdf->SetXY(17,170+$X1);
$pdf->Cell(180, 5, utf8_decode("Documento que se emite en la Ciudad de ").$sede.", a los ".$DIA." (".$d.")".utf8_decode(" días del mes de"), 0, 0, 'L', 0);

}


$pdf->SetXY(17,180+$X1);
$pdf->Cell(180, 5, $MES.utf8_decode(" del año ").$AÑO, 0, 0, 'C', 0);
  
include('db.php');
$sql = "SELECT * FROM directivos where cargo='Jefa de Control de Estudios'"; 
        $resultado = $conn->query($sql); 

        if ($resultado->num_rows > 0) {
              
			while($fila = $resultado->fetch_assoc()) { 
				$jefe_nombre=$fila["nombre"];
                $jefe_cargo=$fila["cargo"];
                $jefe_lugar=$fila["lugar"];
			}

		}

$pdf->SetXY(17,200+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_nombre), 0, 0, 'C', 0);

$pdf->SetFont('Arial','B',11);
$pdf->SetXY(17,205+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_cargo), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(17,220+$X1);
$pdf->Cell(180, 5, utf8_decode("Este Documento"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','B',13);
$pdf->SetXY(47,220+$X1);
$pdf->Cell(180, 5, utf8_decode("NO ES VALIDO"), 0, 0, 'L', 0);

$pdf->SetFont('Arial','',11);
$pdf->SetXY(81,220+$X1);
$pdf->Cell(180, 5, utf8_decode("sin la firma y Sello del Departamento de Control De Estudios"), 0, 0, 'L', 0);


$pdf->Image("LOGOPIES.jpg" , 57 ,250, 100 , 15 , "jpg" ,"");

$pdf->SetFont('Arial','B',10);
$pdf->SetXY(17,255+$X1);
$pdf->Cell(180, 5, utf8_decode("TRANSFORMACIÒN UNIVERSITARIA CON CALIDAD Y PERTENENCIA"), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',9);
$pdf->SetXY(17,260+$X1);
$pdf->Cell(180, 5, utf8_decode("Urbanización la Elvira, Zona Industrial Santa Rosa, Galpón Nº 8, Puerto Cabello"), 0, 0, 'C', 0);

$pdf->SetXY(17,265+$X1);
$pdf->Cell(180, 5, utf8_decode("Número Telefónico: (0242) 3700494. Correo Electrónico: Controldeestudio.IUTPC@gmail.com"), 0, 0, 'C', 0);

$pdf->SetXY(17,280+$X1);
$pdf->Cell(180, 5, utf8_decode("Instituto Universitario de Tecnología de Puerto Cabello Nº de control: 00000000000000007100939 Página (S): 1"), 0, 0, 'C', 0);


$pdf->Output();

}else{
  header("Location: msg_no_activo.php");
}
?>
