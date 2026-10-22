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

$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}

include "db.php";
include('/Classes/class_api.php');

$query = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$usuario1 = $row['nombre'];
}  	

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 
		$codigo = $fila['id'];
		$cedula = strtoupper($fila['cedula']);
		$nombre = strtoupper(utf8_decode($fila['nombre']));
		$carrera = strtoupper(utf8_decode($fila['carrera']));
		$mencion = $fila['mencion'];
		$plan = $fila['plan'];
		$actividad = $fila['actividad'];
		$sexo = $fila['sexo'];
		$edocivil = strtoupper($fila['edocivil']);
		$lugar = strtoupper(utf8_decode($fila['lugar']));
		$municipio = strtoupper(utf8_decode($fila['municipio']));
		$estado = strtoupper(utf8_decode($fila['estado']));
		$procedenci = strtoupper(utf8_decode($fila['procedenci']));
		$fechanac = $fila['fechanac'];
		$edad = $fila['edad'];
		$direccion = strtoupper(utf8_decode($fila['direccion']));
		$telefonoh = $fila['telefonoh'];
		$telefonoc = $fila['telefonoc'];
		$telefonot = $fila['telefonot'];
		$email = utf8_decode($fila['email']);
		$tipingreso = strtoupper(utf8_decode($fila['tipingreso']));
		$ingreso = $fila['ingreso'];
		$semestre = $fila['semestre'];
		$egreso = $fila['egreso'];
		$pasantia = $fila['pasantia'];
		$turno = utf8_decode($fila['turno']);
		$trabajo = $fila['trabajo'];
		$beca = $fila['beca'];
		$ireceptor = strtoupper($fila['ireceptor']);
		$folio = $fila['folio'];
		$tomo = $fila['tomo'];
		$rusnies = $fila['rusnies'];
		$discapacid = $fila['discapacid'];
		$pnf = $fila['pnf'];
		$trayecto = $fila['trayecto'];
	    $fcedula = $fila['fcedula'];
        $inscripmilt = $fila['inscripmilt'];
        $ftitulo = $fila['ftitulo'];
        $fcerfidicado = $fila['fcerfidicado'];
        $fnotas = $fila['fnotas'];
        $fdosfotos = $fila['fdosfotos'];
        $fpinscrip = $fila['fpinscrip'];
        $fdepbanc = $fila['fdepbanc'];
        $fnacimie = $fila['fnacimie'];
	}  
}	

$pdf = new PDF();

$carrera_a1 = $pdf->carrera_larga($carrera);
$carrera_a2 = $pdf->carrera_corta($carrera);

// echo $carrera_a1."<br>";
// echo $carrera_a2."<br>"; 

$sql = "SELECT * FROM `lapso` WHERE id= (SELECT COUNT(id) FROM `lapso`)";
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {  
		$nombre_lapso= $fila['descrip'];
		$lapso_actual= $fila['lapso'];

	}  
}




$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
// $pdf = new FPDF();
$pdf->AddPage();

date_default_timezone_set('America/Caracas');
$fechaActual = date('d-m-Y');   
$d=date("d");
$m=date("m");
$y=date("Y");


//strtolower($AÑO)
//nombremes($MES)




$X1=5;

$pdf->SetFont('Arial','',14);
$pdf->SetXY(5,0+$X1);
$pdf->Cell(136, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','',14);
$pdf->SetXY(5,4+$X1);
$pdf->Cell(136, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','',12);
$pdf->SetXY(5,8+$X1);
$pdf->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','B',14);

$pdf->SetXY(17,23+$X1);
$pdf->Cell(180, 5, utf8_decode("PLANILLA DE ADMISION LAPSO ").substr($lapso_actual, 0, 4), 0, 0, 'C', 0);

$pdf->SetFont('Arial','',12);
$pdf->SetXY(17,30+$X1);
$pdf->Cell(180, 5, utf8_decode("DATOS PERSONALES"), 0, 0, 'C', 0);


$pdf->SetFont('Arial','',11);
$pdf->SetXY(13,30+$X1);
$pdf->Cell(180, 5, utf8_decode("CÓDIGO: ").$codigo, 0, 0, 'R', 0);
$pdf->Line(19, 41, 195, 41);

       
$X0=-5;
$pdf->SetFont('Arial','',12);
$pdf->SetXY(17,50+$X0);
$pdf->Cell(180, 5, utf8_decode("CEDULA:  ").$cedula, 0, 0, 'L', 0);
$pdf->Line(38, 55.5+$X0, 62, 55.5+$X0);

$pdf->SetXY(68,50+$X0);
$pdf->Cell(180, 5, utf8_decode("NOMBRE: ").$nombre, 0, 0, 'L', 0);
$pdf->Line(89, 55.5+$X0, 195, 55.5+$X0);

$X0=5;

$pdf->SetXY(17,50+$X0);
$pdf->Cell(180, 5, utf8_decode("FEC. NAC.: ").$fechanac, 0, 0, 'L', 0);
$pdf->Line(38, 55.5+$X0, 62, 55.5+$X0);

$X0=5;
$pdf->SetXY(65,50+$X0);
$pdf->Cell(180, 5, utf8_decode("SEXO: ").$sexo, 0, 0, 'L', 0);

$Y1=25;
$pdf->SetXY(65+$Y1,50+$X0);
$pdf->Cell(180, 5, utf8_decode("EST.DE NAC.:  ").$estado, 0, 0, 'L', 0);
$Y1=50;
$pdf->Line(70+$Y1, 55.5+$X0, 145+$Y1, 55.5+$X0);

$X0=15;

$pdf->SetXY(17,50+$X0);                    
$pdf->Cell(180, 5, utf8_decode("LUGAR DE NAC.: ").substr($lugar, 0, 19), 0, 0, 'L', 0);
$pdf->Line(53, 55.5+$X0, 102, 55.5+$X0);


$pdf->SetXY(105,50+$X0);
$pdf->Cell(180, 5, utf8_decode("MUNICIPIO DE HAB.: ").$municipio, 0, 0, 'L', 0);
$pdf->Line(150, 55.5+$X0, 195, 55.5+$X0);

$X0=25;
$pdf->SetXY(17,50+$X0);
$pdf->Cell(180, 5, utf8_decode("DIRECCIÓN: ").$direccion, 0, 0, 'L', 0);
$pdf->Line(44, 55.5+$X0, 195, 55.5+$X0);

$X0=35;
$pdf->SetXY(17,50+$X0);
$pdf->Cell(180, 5, utf8_decode("TELF. CEL.:  ").$telefonoc, 0, 0, 'L', 0);
$pdf->Line(44, 55.5+$X0, 80, 55.5+$X0);

$Y1=68;
$pdf->SetXY(12+$Y1,50+$X0);
$pdf->Cell(180, 5, utf8_decode("TELF. HAB.:  ").$telefonoh, 0, 0, 'L', 0);
$pdf->Line(37+$Y1, 55.5+$X0, 70+$Y1, 55.5+$X0);


$Y1=125;
$pdf->SetXY(12+$Y1,50+$X0);
$pdf->Cell(180, 5, utf8_decode("TELF. TRA.:  ").$telefonot, 0, 0, 'L', 0);
$pdf->Line(37+$Y1, 55.5+$X0, 70+$Y1, 55.5+$X0);

$X0=20;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, "CORREO ELECT.: ".$email, 0, 0, 'L', 0);
$pdf->Line(55, 100, 160,100);

$X1=15;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("CARRERA Y MENCIÓN SOLICITADA:"), 0, 0, 'C', 0);

$pdf->SetXY(160,90+$X1);
$pdf->Cell(180, 5, utf8_decode("TURNO: ").$turno, 0, 0, 'L', 0);


$pdf->SetXY(17,100+$X1);
$pdf->Cell(180, 5, utf8_decode($carrera_a1), 0, 0, 'C', 0);
$pdf->Line(55, 120, 160,120);


$X1=35;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("MEN.BAC.:"), 0, 0, 'L', 0);
$pdf->Line(40, 130, 90,130);
$Y1=75;

$pdf->SetXY(17+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("TIP.INS.:               AÑO EGRESO BAC.:       "), 0, 0, 'L', 0);
$pdf->Line(173, 130, 195,130);

$X1=45;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("PROCEDENCIA: ").$procedenci, 0, 0, 'L', 0);
$pdf->Line(50, 140, 90,140);
$Y1=75;

$pdf->SetXY(17+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("TIPO DE INGRESO:  ").$tipingreso, 0, 0, 'L', 0);
$pdf->Line(133, 140, 195,140);


$X1=55;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("NIVEL SOCIO-ECONOMICO:"), 0, 0, 'L', 0);
$pdf->Line(75, 150, 140,150);
$Y1=75;

$pdf->SetXY(70+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("TRABAJA: ").$trabajo, 0, 0, 'L', 0);

$pdf->Line(18, 155, 195, 155);

$X1=70;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("DOCUMENTOS CONSIGNADOS"), 0, 0, 'C', 0);

$pdf->Line(18, 170, 195, 170);

$X1=85;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("1. F.CEDULA DE IDENTIDAD          :    ").$fcedula, 0, 0, 'L', 0);
$pdf->Line(89, 180, 102,180);
$Y1=75;

$pdf->SetXY(35+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("6. F.INSCRIPCION MILITAR         :      ").$inscripmilt, 0, 0, 'L', 0);
$pdf->Line(180, 180, 195,180);

$X1=95;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("2. F.TITULO DE BACHILLER           :    ").$ftitulo, 0, 0, 'L', 0);
$pdf->Line(89, 190, 102,190);
$Y1=75;

$pdf->SetXY(35+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("7. F.CERTIFICADO DE SALUD    :      ").$fcerfidicado, 0, 0, 'L', 0);
$pdf->Line(180, 190, 195,190);

$X1=105;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("3. F.NOTAS CERTIFICADAS           :    ").$fnotas, 0, 0, 'L', 0);
$pdf->Line(89, 200, 102,200);
$Y1=75;

$pdf->SetXY(35+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("8. DOS FOTOS TIPO CARNET    :      ").$fdosfotos, 0, 0, 'L', 0);
$pdf->Line(180, 200, 195,200);

$X1=115;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("4. F.PLANILLA DE INSCRIP.CNU    :    ").$fpinscrip, 0, 0, 'L', 0);
$pdf->Line(89, 210, 102,210);
$Y1=75;

$pdf->SetXY(35+$Y1,90+$X1);
$pdf->Cell(180, 5, utf8_decode("9. DEP.BANCARIO (ORIGINAL)   :      ").$fdepbanc, 0, 0, 'L', 0);
$pdf->Line(180, 210, 195,210);

$X1=125;
$pdf->SetXY(17,90+$X1);
$pdf->Cell(180, 5, utf8_decode("5. F.PARTIDA DE NACIMIENTO      :    ").$fnacimie, 0, 0, 'L', 0);
$pdf->Line(89, 220, 102,220);
$Y1=75;

$pdf->Line(18, 225, 195, 225);

$pdf->SetXY(17,232);
$pdf->Cell(180, 5, utf8_decode("DOCUMENTOS REVISADOS POR: "), 0, 0, 'L', 0);
$pdf->Line(140, 235,195,235);


$pdf->SetXY(17,240);
$pdf->Cell(180, 5, utf8_decode("REGISTRO PROCESADO POR: "), 0, 0, 'L', 0);
$pdf->SetXY(147,240);
$pdf->Cell(180, 5, $usuario1, 0, 0, 'L', 0);

$pdf->Line(140, 245,195,245);


$pdf->SetXY(17,255);
$pdf->Cell(180, 5, utf8_decode("Nota: La planilla no es válida sin las dos firmas y el sello de la unidad"), 0, 0, 'C', 0);

$pdf->SetXY(17,262);
$pdf->Cell(180, 5, utf8_decode("ESTE DOCUMENTO ES VALIDO COMO COSTANCIA DE ESTUDIO"), 0, 0, 'C', 0);

$pdf->Output();



?>
