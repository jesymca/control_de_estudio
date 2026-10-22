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


require ('fpdf.php'); 
require ('num2letras.php');

require ("aud.php");
auditar("ACTAS_FINAL",$_POST["cod_mat"]." - ".$_POST["lapso"]);


class PDF extends FPDF
{

	function Encabezado()
	{

		$this->AddPage();
		$this->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
		date_default_timezone_set('America/Caracas');
		$fechaActual = date('d-m-Y');   
		$DIA=date("d");
		$MES=date("m");
		$AÑO=date("Y");

		date_default_timezone_set('UTC');
		$hoy = date("d-m-Y");
		
		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"];        
		$TIPO =$_POST["tiplap"];
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];
		
		$carrera=substr($MATERIA, 0, 1);




        //$this->SetFillColor(0,0,0);
		$this->SetFont('Arial','B',12);


		$X1=5;
		$this->Image("LOGO.jpg" , 10 ,5+$X1, 43 , 25 , "jpg" ,"");

		$this->SetXY(54,0+$X1);
		$this->Cell(136, 5, utf8_decode("REPÚBLICA BOLIVARIANA DE VENEZUELA"), 0, 0, 'L', 0);
		$this->SetFont('Arial','',10);
		$this->SetXY(54,4+$X1);
		$this->Cell(136, 5, utf8_decode("MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA"), 0, 0, 'L', 0);
		$this->SetXY(54,8+$X1);
		$this->Cell(136, 5, utf8_decode("CIENCIA Y TECNOLOGÍA"), 0, 0, 'L', 0);

		$this->SetXY(54,15+$X1);
		$this->Cell(136, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'L', 0);
		$this->SetXY(54,19+$X1);
		$this->Cell(136, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'L', 0);
		$this->SetXY(54,23+$X1);
		$this->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);

		$this->SetFont('Arial','B',10);
		
		$this->SetXY(55,35+$X1);
		$this->Cell(100, 7,"LISTADO DE ALUMNOS PARA LA CARGA DE NOTAS ".substr($LAPSO, 0, 4)." ".$TIPO, 0, 0, 'C', 0);


		$this->SetFont('Arial','',8);
		$this->SetXY(15,45+$X1);
		$this->Cell(136, 5, utf8_decode("ASIGNATURA:"), 0, 0, 'L', 0);

		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"]; 
		
		$pnf=$_POST["pnf"];

		$TIPO =$_POST["tiplap"];
		$cod_doc=$_POST["cod_doc"];	

		
		$COD=substr($MATERIA, 0, 5);
		


		
		$SECCION=$_POST["seccion"];
		$carrera=substr($MATERIA, 0, 1);


		include "db.php";
		$sql = "SELECT * FROM lismat WHERE cod_mat='".$MATERIA."'";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {                      
				$DESCRIP2  = utf8_decode($fila['descrip2']);
				$CRED  = $fila['creditos'];
				$semestre = $fila['semestre'];
				$pensum	= $fila['pensum'];	
				$aprobatori	= $fila['aprobatori'];	
			}  
		}		



		$this->SetXY(38,45+$X1);
		$this->Cell(250, 5, substr($DESCRIP2, 0, 31)." (".$COD.")", 0, 0, 'L', 0);

		$this->SetXY(105,45+$X1);
		$this->Cell(111, 5, utf8_decode("SECCIÓN: ").$semestre." - ".$SECCION, 0, 0, 'L', 0);

		$this->SetXY(167,45+$X1);
		$this->Cell(20, 5, utf8_decode("FECHA: ").$fechaActual, 0, 0, 'L', 0);


		$sql = "SELECT * FROM docente WHERE cod_doc='".$cod_doc."'";
		$resultado = $conn->query($sql);


		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 		
				$CEDULA  = utf8_decode($fila['cedula']);
				$NOMBRE  = utf8_decode($fila['nombre']);
			}
		}

		$this->SetXY(15,53+$X1);
		$this->Cell(136, 5, "DOCENTE:", 0, 0, 'L', 0);
		$this->SetXY(35,53+$X1);	
		$this->Cell(136, 5, $NOMBRE."  (".$cod_doc.")", 0, 0, 'L', 0);

		$this->SetLineWidth(0.1);
		$this->Line(35,63,97,63);
		
		$this->SetXY(105,53+$X1);
		$this->Cell(15, 5, utf8_decode("CÉDULA:"), 0, 0, 'L', 0);
		
		$this->SetXY(120,53+$X1);
		$this->Cell(30, 5, $CEDULA, 0, 0, 'L', 0);

		$this->Line(120,63,137,63);
		$this->SetLineWidth(0.1);

		$this->SetXY(145,53+$X1);
		$this->Cell(136, 5, "FIRMA:", 0, 0, 'L', 0);

		$this->Line(157,63,195,63);
		$this->SetLineWidth(0.1);

		$this->SetFont('Arial','B',8);


		$this->SetXY(15,60+$X1);
		$this->Cell(90, 5, "DATOS DEL ALUMNO", 1, 0, 'C', 0);

		$this->SetXY(105,60+$X1);
		$this->Cell(40, 5, "NOTAS ADQUIRIDAS", 1, 0, 'C', 0);

		$this->SetXY(15.3,65+$X1);
		$this->Cell(5, 5, "#", 0, 0, 'C', 0);

		$this->SetXY(20,65+$X1);
		$this->Cell(17, 5, "CEDULA", 1, 1, 'C', 0);

		$this->SetXY(37,65+$X1);
		$this->Cell(68, 5, "NOMBRE DEL ALUMNO", 1, 1, 'C', 0);

		$this->SetXY(15,65+$X1);
		$this->Cell(90, 5,"", 1, 1, 'L', 0);

		// $this->SetXY(105,60+$X1);
		// $this->Cell(90, 10, "", 1, 1, 'L', 0);
		
		$this->SetXY(145,60+$X1);
		$this->Cell(50, 10, "OBSERVACION", 1, 1, 'C', 0);

		$this->SetXY(105,65+$X1);
		$this->Cell(10, 5, "1", 1, 0, 'C', 0);

		$this->SetXY(115,65+$X1);
		$this->Cell(10, 5, "2", 1, 0, 'C', 0);

		$this->SetXY(125,65+$X1);
		$this->Cell(10, 5, "3", 1, 0, 'C', 0);

		$this->SetXY(135,65+$X1);
		$this->Cell(10, 5, "T", 1, 0, 'C', 0);
	}

	
	
}

$LAPSO=$_POST["lapso"];
$MATERIA=$_POST["cod_mat"];        
$TIPO =$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];


if ($cod_doc != '' and $MATERIA != ''){

	$pdf=new PDF();
	$pdf->Encabezado();	
	
	$DOCENTE= $cod_doc;
	$NUM  = '';
	$CODIGO = '';
	$CEDULA  = '';
	$NOMBRE  = '';
	$ACUM     = '';
	$NOTA     = '';
	$LETRA     = '';
	$Observaciones     = '';


	include "db.php";
	$sql = "SELECT * FROM lismat WHERE cod_mat='".$MATERIA."'";
	$resultado = $conn->query($sql);

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {                      
			$DESCRIP2  = utf8_decode($fila['descrip2']);
			$CRED  = $fila['creditos'];
			$semestre = $fila['semestre'];
			$pensum	= $fila['pensum'];	
			$aprobatori	= $fila['aprobatori'];	
		}  
	}	

	$carrera=substr($MATERIA, 0, 1);
	$plan=substr($MATERIA, 4, 1);
	$SECCION=$_POST["seccion"];

	$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre FROM notas,alumno WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$MATERIA."' and notas.lapso='".$LAPSO."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and alumno.carrera='".$carrera."' and alumno.plan='".$plan."' ORDER BY alumno.nombre";


	$resultado = $conn->query($sql);

	$X1B=3;
	$IN=0;
	$APRO=0;
	$REPR=0;

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {   


			$pdf->SetFont('Arial','',8);

			$X0=$X0+1;
			$X1B=$X1B+5;

			$pdf->SetXY(15,67+$X1B);
			$pdf->Cell(90, 5, $X0, 1, 1, 'L', 0);

			$pdf->SetXY(20,67+$X1B);		
			$pdf->Cell(17, 5, $fila['cedula'], 1, 1, 'L', 0);

			$pdf->SetXY(37,67+$X1B);
			$pdf->Cell(120, 5, utf8_decode($fila['nombre']), 0, 0, 'L', 0);

			$pdf->SetXY(105,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);

			$pdf->SetXY(115,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);

			$pdf->SetXY(125,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);

			$pdf->SetXY(135,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			
			$pdf->SetXY(145,67+$X1B);
			$pdf->Cell(50, 5, "", 1, 1, 'L', 0);		



			if($X0>40){
				$X0=0;
				$pdf->Encabezado();			
				
				$X1B=3;         
				

			}

		}
	}

	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(15,72+$X1B);
	$pdf->Cell(10, 5, "Nota:", 0, 0, 'L', 0);	
	$pdf->SetFont('Arial','',8);	
	$pdf->SetXY(24,72+$X1B);
	$pdf->Cell(10, 5, utf8_decode("La escala de puntuación debe ser del 1 al 20."), 0, 0, 'L', 0);

	$pdf->Output();
}

?>
