<?php


if(substr($MATERIA, 1, 1)=="P" OR substr($MATERIA, 1, 1)=="T" OR substr($MATERIA, 1, 1)=="E"){
	$TIPO=substr($MATERIA, 1, 1);
}else{
	$TIPO="";
}

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

$lapso=$_POST["lapso"];
$cod_mat=$_POST["cod_mat"];        
$cod_doc=$_POST["cod_doc"];		
$seccion=$_POST["seccion"];	




$pdf=new PDF();                 
$pdf->encabezado("LISTADO DE ALUMNO POR MATERIA ".substr($lapso, 0, 4));	
	
$X0_2=0;
include "db.php";
       

	$sql = "SELECT nombre FROM docente WHERE cod_doc='".$cod_doc."'";
	$resultado = $conn->query($sql);


	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) { 				
			$nombre_docente  = utf8_decode($fila['nombre']);
		}
	}


        $pdf->SetFont('Arial','B',8);

		$pdf->SetXY(15,65+$X1);
		$pdf->Cell(180, 10, "", 1, 1, 'L', 0);

        $pdf->SetXY(15,70+$X1);
		$pdf->Cell(136, 5, utf8_decode("NUM."), 0, 0, 'L', 0);

		$pdf->SetXY(30,70+$X1);
		$pdf->Cell(136, 5, utf8_decode("CEDULA"), 0, 0, 'L', 0);

		$pdf->SetXY(55,70+$X1);
		$pdf->Cell(136, 5, utf8_decode("NOMBRE DEL ALUMNO"), 0, 0, 'L', 0);



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


    $pdf->SetFont('Arial','',8);
 
    $pdf->SetXY(15,60+$X1);
	$pdf->Cell(50, 5, utf8_decode("Asignatura:"), 0, 0, 'L', 0);
    $pdf->SetXY(31,60+$X1);
	$pdf->Cell(136, 5, $DESCRIP2." (".$cod_mat.")", 0, 0, 'L', 0);

    $pdf->SetXY(15,55+$X1);
	$pdf->Cell(50, 5, utf8_decode("Nombre:"), 0, 0, 'L', 0);
    $pdf->SetXY(31,55+$X1);
	$pdf->Cell(136, 5, $nombre_docente." (".$cod_doc.")", 0, 0, 'L', 0);

    $pdf->SetXY(175,60+$X1);
	$pdf->Cell(50, 5, utf8_decode("Seccion:"), 0, 0, 'L', 0);
    $pdf->SetXY(187,60+$X1);
	$pdf->Cell(136, 5, $seccion, 0, 0, 'L', 0);

	$carrera=substr($cod_mat, 0, 1);
	$plan=substr($cod_mat, 4, 1);


	$sql = "SELECT DISTINCT alumno.cedula,alumno.nombre FROM notas,alumno WHERE notas.seccion='".$seccion."' and notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo ORDER BY alumno.nombre";

	$resultado = $conn->query($sql);

	$X1B=5;	
	
	$X0=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {   

			$pdf->SetFont('Arial','',8);
			$X0=$X0+1;			
			$X1B=$X1B+4;

			$pdf->SetXY(15,67+$X1B);
			$pdf->Cell(136, 5, $X0, 0, 0, 'L', 0);

			$pdf->SetXY(30,67+$X1B);		
			$pdf->Cell(136, 5, $fila['cedula'], 0, 0, 'L', 0);

			$pdf->SetXY(55,67+$X1B);
			$nombre= strtoupper($fila['nombre']);
			$pdf->Cell(136, 5, substr(utf8_decode($nombre), 0, 39), 0, 0, 'L', 0);
            $X0_2=$X0_2+1;

			if($X0_2>41){

				$pdf->encabezado($lapso,$cod_mat,$cod_doc,$seccion,"LISTADO DE ALUMNO POR MATERIA");	
				$X1B=5;
				$X0_2=0;
			}

		}
	}

	
	$pdf->Output();
	


?>
