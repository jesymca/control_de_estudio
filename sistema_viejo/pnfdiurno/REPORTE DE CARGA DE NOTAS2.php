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
		



        $LAPSO=$_POST["buscar2"]; 

        $pdf->reporte_de_carga_de_nota_Encabezado("Docentes sin carga de notas",$X0_3,$_POST["buscar2"]);
        
        $X1=-9;
        $X0=0;
        $X0_2=1;
        include "db.php"; 
        
		$sql = "SELECT DISTINCT docente.cedula,docente.nombre,docente.cod_doc,notas.cod_mat,lismat.descrip2,notas.lapso,notas.seccion FROM notas,docente,lismat WHERE notas.nota='0' AND notas.lapso='".$LAPSO."' AND docente.cod_doc=notas.cod_doc AND notas.cod_mat=lismat.cod_mat ORDER BY docente.nombre ASC";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 		
			  $cedula=$fila['cedula'];
			  $nombre=$fila['nombre'];
			  $cod_doc=$fila['cod_doc'];
			  $cod_mat=$fila['cod_mat'];
			  $descrip2=$fila['descrip2'];
			  $seccion=$fila['seccion'];
			  $lapso=$fila['lapso'];
              
		
        $pdf->SetFont('Arial','',8);		
		$pdf->SetXY(5,65+$X1);
		$pdf->Cell(6, 5,$X0_2 , 1, 1, 'C', 0);

		$pdf->SetXY(11,65+$X1);
		$pdf->Cell(17, 5, $cedula, 1, 1, 'C', 0);

		$pdf->SetXY(28,65+$X1);
		$pdf->Cell(55, 5, utf8_decode(substr($nombre, 0, 28)), 1, 1, 'C', 0);

		$pdf->SetXY(83,65+$X1);
		$pdf->Cell(14, 5, $cod_doc, 1, 1, 'C', 0);

		$pdf->SetXY(97,65+$X1);
		$pdf->Cell(14, 5, $cod_mat, 1, 1, 'C', 0);

        $pdf->SetXY(111,65+$X1);
		$pdf->Cell(87, 5,utf8_decode(substr($descrip2, 0, 47)), 1, 1, 'C', 0);

        $pdf->SetXY(198,65+$X1);
		$pdf->Cell(7, 5, $seccion, 1, 1, 'C', 0);   

		$X0++;
		$X0_2++;
		$X1=$X1+5;

		if($X0>36){
        $X0_3=$X0_3+1;
		$pdf->reporte_de_carga_de_nota_Encabezado("Docentes sin carga de notas",$X0_3,$_POST["buscar2"]);
		$X1=-9;
        $X0=0;
		}

	}
}        



$pdf->SetFont('Arial','B',11);  

$R=110;
$R2=22;
$X1=60;
$sql = "SELECT * FROM directivos where cargo='Jefa de Control de Estudios'"; 
		$resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
			
			while($fila = $resultado->fetch_assoc()) { 
				$jefe_nombre=$fila["nombre"];
				$jefe_cargo=$fila["cargo"];
				$jefe_lugar=$fila["lugar"];
			}

		}


$pdf->Line(117+$R2, 199+$X1, 172+$R2, 199+$X1);
$pdf->SetXY(33+$R,200+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_nombre), 0, 0, 'L', 0);
$pdf->SetFont('Arial','',11); 
$pdf->SetXY(32+$R,205+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_cargo), 0, 0, 'L', 0);

$pdf->SetXY(34+$R,210+$X1);
$pdf->Cell(180, 5, utf8_decode($jefe_lugar), 0, 0, 'L', 0);



	$pdf->Output();



function nombremes($mes1)
{
	setlocale(LC_TIME, 'spanish');
	$mes = strftime("%B", mktime(0, 0, 0, $mes1, 1, 2000));
	return $mes;
}


?>
