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

require ("aud.php");
auditar("ACTAS_FINAL",$_POST["cod_mat"]." - ".$_POST["LAPSO"]);


$lapso=$_POST["lapso"];
$cod_mat=$_POST["cod_mat"];        
$tipo =$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];

include('/Classes/class_api.php');
$pdf=new PDF();

$pdf->Encabezado_nomina_asistencia($cod_mat,$cod_doc,$lapso,$seccion);

// echo "LAPSO: ".$lapso."<br>";
// echo "MATERIA: ".$cod_mat."<br>";
// echo "TIPO: ".$tipo."<br>";
// echo "cod_doc: ".$cod_doc."<br>";



if ($cod_doc != '' and $cod_mat != ''){

	$pdf=new PDF();
	$pdf->Encabezado_nomina_asistencia($cod_mat,$cod_doc,$lapso,$seccion);	
	
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
	$sql = "SELECT * FROM lismat WHERE cod_mat='".$cod_mat."'";
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

	$carrera=substr($cod_mat, 0, 1);
	$plan=substr($cod_mat, 4, 1);
	$SECCION=$_POST["seccion"];



// echo "carrera: ".$carrera."<br>";
// echo "plan: ".$plan."<br>";
// echo "SECCION: ".$SECCION."<br>";

	$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre FROM notas,alumno WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and alumno.carrera='".$carrera."' and alumno.plan='".$plan."' ORDER BY alumno.nombre";


	$resultado = $conn->query($sql);

	$X1B=3;
	$IN=0;
	$APRO=0;
	$REPR=0;

	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {   


			$pdf->SetFont('Arial','',7);

			$X0=$X0+1;
			$X1B=$X1B+5;

			$pdf->SetXY(15,67+$X1B);
			$pdf->Cell(95, 5, $X0, 1, 1, 'L', 0);

			$pdf->SetXY(20,67+$X1B);		
			$pdf->Cell(15, 5, $fila['cedula'], 1, 1, 'L', 0);

			$pdf->SetXY(35,67+$X1B);
			$pdf->Cell(70, 5, utf8_decode($fila['nombre']), 0, 0, 'L', 0);

			$pdf->SetXY(105,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);

			$pdf->SetXY(110,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(115,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(120,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(125,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(130,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(135,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(140,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(145,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(150,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(155,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);
			$pdf->SetXY(160,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);   
			$pdf->SetXY(165,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0);  
			$pdf->SetXY(170,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0); 
			$pdf->SetXY(175,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0); 
			$pdf->SetXY(180,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0); 
			$pdf->SetXY(185,67+$X1B);
			$pdf->Cell(10, 5, "", 1, 1, 'L', 0); 

            $X1=$X1+1;

			if($X1>37){

				$pdf->Encabezado_nomina_asistencia($cod_mat,$cod_doc,$lapso,$seccion);
				$X1B=5;
				$X1=1;

			}

		}
	}

	$pdf->Output();
}
?>
