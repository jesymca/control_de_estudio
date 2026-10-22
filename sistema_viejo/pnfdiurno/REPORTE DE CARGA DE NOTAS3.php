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



class PDF extends FPDF
{

	function Encabezado()
	{

		$this->AddPage();
		date_default_timezone_set('America/Caracas');
		$fechaActual = date('d-m-Y');   
		$DIA=date("d");
		$MES=date("m");
		$AÑO=date("Y");

		date_default_timezone_set('UTC');
		$hoy = date("d-m-Y");
		
	
        //$this->SetFillColor(0,0,0);
		$this->SetFont('Arial','B',12);


		$X1=5;
		$this->Image("logoiutpc2.jpg" , 10 ,0+$X1, 45 , 25 , "jpg" ,"");

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
		$this->Cell(100, 7,"REPORTE DE CARGA DE NOTAS", 0, 0, 'C', 0);


	
        $this->SetFont('Arial','',7);
		$this->SetXY(170,45);
		$this->Cell(136, 5, utf8_decode("FECHA: "), 0, 0, 'L', 0);
       
        $this->SetXY(180,45);
		$this->Cell(136, 5, $hoy, 0, 0, 'L', 0);

        $this->SetFont('Arial','B',7);
        $X1=-15;
		$this->SetXY(15,64+$X1);
		$this->Cell(180, 7, "", 1, 1, 'L', 0);

		$this->SetXY(15,64+$X1);
		$this->Cell(5, 7, utf8_decode("N°"), 1, 1, 'C', 0);

		$this->SetXY(20,64+$X1);
		$this->Cell(16, 7, utf8_decode("CEDULA"), 1, 1, 'C', 0);

		$this->SetXY(36,64+$X1);
		$this->Cell(45, 7, utf8_decode("NOMBRE"), 1, 1, 'C', 0);


		$this->SetXY(81,64+$X1);
		$this->Cell(7, 7, utf8_decode("DOC"), 1, 1, 'C', 0);

		$this->SetXY(88,64+$X1);
		$this->Cell(12, 7, utf8_decode("MAT"), 1, 1, 'C', 0);

		$this->SetXY(100,64+$X1);
		$this->Cell(72, 7, utf8_decode("ASIGNACION"), 1, 1, 'C', 0);

		$this->SetXY(172,64+$X1);
		$this->Cell(7, 7, utf8_decode("SEC"), 1, 1, 'C', 0);

		$this->SetXY(179,64+$X1);
		$this->Cell(16, 7, utf8_decode("LAPSO"), 1, 1, 'C', 0);
		

	}	
}

	$pdf=new PDF();
	$pdf->Encabezado();	



        $LAPSO=$_POST["buscar2"]; 
        $LAPSO="2018-2";
        $X1=-9;
        $X0=0;
        $X0_2=1;
        include "db.php"; 
        
		$sql = "SELECT DISTINCT docente.cedula,docente.nombre,docente.cod_doc FROM notas,docente,lismat WHERE notas.nota<>'0' AND notas.lapso='".$LAPSO."' AND docente.cod_doc=notas.cod_doc AND notas.cod_mat=lismat.cod_mat ORDER BY docente.nombre ASC";
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
              
		
        $pdf->SetFont('Arial','',7);		
		$pdf->SetXY(15,65+$X1);
		$pdf->Cell(5, 5,$X0_2 , 1, 1, 'C', 0);

		$pdf->SetXY(20,65+$X1);
		$pdf->Cell(16, 5, $cedula, 1, 1, 'C', 0);

		$pdf->SetXY(36,65+$X1);
		$pdf->Cell(45, 5, utf8_decode(substr($nombre, 0, 25)), 1, 1, 'C', 0);

		$pdf->SetXY(81,65+$X1);
		$pdf->Cell(7, 5, $cod_doc, 1, 1, 'C', 0);

		$pdf->SetXY(88,65+$X1);
		$pdf->Cell(12, 5, $cod_mat, 1, 1, 'C', 0);

        $pdf->SetXY(100,65+$X1);
		$pdf->Cell(72, 5,utf8_decode(substr($descrip2, 0, 43)), 1, 1, 'C', 0);

        $pdf->SetXY(172,65+$X1);
		$pdf->Cell(7, 5, $seccion, 1, 1, 'C', 0);

        $pdf->SetXY(179,65+$X1);
		$pdf->Cell(16, 5, $lapso, 1, 1, 'C', 0);

		$X0++;
		$X0_2++;
		$X1=$X1+5;

		if($X0>43){

		$pdf->Encabezado();
		$X1=-9;
        $X0=0;
		}

	}
}        


	$pdf->Output();



?>
