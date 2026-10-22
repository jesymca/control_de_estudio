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
date_default_timezone_set('America/Caracas');
$fechaActual = date('d/m/Y');   
$d=date("d");
$m=date("m");
$y=date("Y");

$DIA = num2letras($d);
$AÑO = num2letras($y);
//strtolower($AÑO)
//nombremes($MES)

function nombremes($mes1)
{
	setlocale(LC_TIME, 'spanish');
	$mes = strftime("%B", mktime(0, 0, 0, $mes1, 1, 2000));
	return $mes;
}



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
		
		$LAPSO=$_POST["LAPSO"];
		$MATERIA=$_POST["cod_mat"];        
		$TIPO =$_POST["tiplap"];
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];
		
		$carrera=substr($MATERIA, 0, 1);


include('getpensum3_clase.php');
$con = new carreras();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);
		



       
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
		
		$this->SetXY(65,35+$X1);
		$this->Cell(80, 5,"CERTIFICACION DE NOTAS", 1, 1, 'C', 0);
        
        $this->SetFont('Arial','',8);
        $X1=7;
        $this->SetXY(15,40+$X1);
		$this->Cell(80, 5,"APELLIDOS Y NOMBRES", 1, 1, 'C', 0);
		$this->SetXY(15,45+$X1);
		$this->Cell(80, 5,"GUERRA FLORES JOSE ALEJANDRO", 1, 1, 'C', 0);
        
        $this->SetXY(95,40+$X1);
		$this->Cell(20, 5,"CEDULA", 1, 1, 'C', 0);
		$this->SetXY(95,45+$X1);
		$this->Cell(20, 5,"V20980961", 1, 1, 'C', 0);

		$this->SetXY(115,40+$X1);
		$this->Cell(80, 5,"PROGRAMA NACIONAL DE FORMACION", 1, 1, 'C', 0);
		$this->SetXY(115,45+$X1);
		$this->Cell(80, 5,"INGENIERIA DE MATERIALES INDUSTRIALES", 1, 1, 'C', 0);


        $this->SetXY(55,50+$X1);
		$this->Cell(100, 5,"SIGNATURAS CURSADAS", 0, 0, 'C', 0);
     
       
       ///////////////////////////////////
		$this->SetXY(15,55+$X1);
		$this->Cell(180, 5,"", 1, 1, 'C', 0);

        $this->SetXY(15,55+$X1);
		$this->Cell(30, 5,"CODIGO", 0, 0, 'L', 0);

        $this->SetXY(32,55+$X1);
		$this->Cell(20, 5,"SECCION", 0, 0, 'L', 0);
        
        $this->SetXY(50,55+$X1);
		$this->Cell(20, 5,"NOMBRE DE LA ASIGNATURA", 0, 0, 'L', 0);
        
        $this->SetXY(120,55+$X1);
		$this->Cell(20, 5,"TRAYECTO", 0, 0, 'L', 0);
        
        $this->SetXY(138,55+$X1);
		$this->Cell(20, 5,"UC", 0, 0, 'L', 0);
        
        $this->SetXY(145,55+$X1);
		$this->Cell(20, 5,"NOTAS", 0, 0, 'L', 0);
        
        $this->SetXY(157,55+$X1);
		$this->Cell(20, 5,"LAPSO", 0, 0, 'L', 0);
        
        $this->SetXY(170,55+$X1);
		$this->Cell(20, 5,"OBSERVACION", 0, 0, 'L', 0);

        //////////////////


        $this->SetXY(25,60+$X1);
		$this->Cell(160, 5,"", 1, 1, 'C', 0);

        $this->SetXY(25,60+$X1);
		$this->Cell(30, 5,"Indice de Rendimiento academico Academico: 16.353", 0, 0, 'L', 0);
        $this->SetXY(150,60+$X1);
		$this->Cell(20, 5,"Creditos Aprobados 102", 0, 0, 'L', 0);
        
        $this->SetFont('Arial','',6);
        $this->SetXY(15,65+$X1);
		$this->Cell(20, 5,"Contenido de las Materias Elegibles(Aplica solo a la carrera Pnf Metalurgia/Materiales induntriales)", 0, 0, 'L', 0);
        
for ($i=1; $i < 12; $i++) { 

        $X0=6;
        $this->SetFont('Arial','',6);
        $this->SetXY(15-$X0,70+$X1);
		$this->Cell(15, 5,"Elegible 1", 1, 1, 'C', 0);       

        $this->SetXY(30-$X0,70+$X1);
		$this->Cell(22, 5,"2014-1", 1, 1, 'C', 0);
		$this->SetXY(52-$X0,70+$X1);
		$this->Cell(22, 5,"2014-2", 1, 1, 'C', 0);
    	$this->SetXY(74-$X0,70+$X1);
		$this->Cell(22, 5,"2014-3", 1, 1, 'C', 0);
       	$this->SetXY(96-$X0,70+$X1);
		$this->Cell(22, 5,"2015-1", 1, 1, 'C', 0);

		$this->SetXY(118-$X0,70+$X1);
		$this->Cell(22, 5,"2015-2", 1, 1, 'C', 0);
		$this->SetXY(140-$X0,70+$X1);
		$this->Cell(22, 5,"2015-3", 1, 1, 'C', 0);
    	$this->SetXY(162-$X0,70+$X1);
		$this->Cell(22, 5,"2016-1", 1, 1, 'C', 0);
       	$this->SetXY(184-$X0,70+$X1);
		$this->Cell(22, 5,"2016-2", 1, 1, 'C', 0);
	
	    $X1=$X1+5;

}
        



	}

	function Piedepagina($X1)
	{

         
    }
  }


	$pdf=new PDF();
	$pdf->Encabezado();		

$X1B=120;
 $pdf->SetFont('Arial','',7);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("Observaciones: Durante el Lapso Academico 2010-2          Observaciones: Durante el Lapso Academico 2012-1 y subsiguientes, tanto para el Trayecto Inicial"), 0, 0, 'L', 0);
$X1B=$X1B+3;

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("(Trayecto Inicial identificado con el Numero Cero (0),          como para Los Trayectos 1 ,2, 3 y 4 correspondientes a los Programas Nacionales de Formacion (PNF)"), 0, 0, 'L', 0);
$X1B=$X1B+3;

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("(Trayecto Inicial identificado con el Numero Cero (0),          como para Los Trayectos 1 ,2, 3 y 4 correspondientes a los Programas Nacionales de Formacion (PNF)"), 0, 0, 'L', 0);
$X1B=$X1B+3;

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("La Escala de Evaluacion era de 01 al 05, siendo la          para las carreras; La Escala de Evaluacion es del 01 al 20"), 0, 0, 'L', 0);
$X1B=$X1B+3;

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("Nota Minima Aprobatoria 03 ptos"), 0, 0, 'L', 0);
$X1B=$X1B+4;

$pdf->SetFont('Arial','B',8);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("Se certifica que el ciudadano(a) identificado(a) con el nombre :"), 0, 0, 'L', 0);
$pdf->SetFont('Arial','B',8);
$pdf->SetXY(110,35+$X1B);
$pdf->Cell(180, 4, utf8_decode($nombre), 0, 0, 'L', 0);
$X1B=$X1B+4;

$pdf->SetFont('Arial','B',8);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("y titular de la cedula de identidad No."), 0, 0, 'L', 0);
$pdf->SetXY(70,35+$X1B);
$pdf->Cell(180, 4, $cedula, 0, 0, 'L', 0);
$X1B=$X1B+4;

$pdf->SetFont('Arial','B',8);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("cursó y culminó todas las asignaturas para obtener el titulo de INGENIERO (A) EN:"), 0, 0, 'L', 0);
$X1B=$X1B+6;

$pdf->SetFont('Arial','B',10);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode($carrera_a2), 0, 0, 'C', 0);
$X1B=$X1B+4;

$pdf->SetFont('Arial','',8);
$pdf->SetXY(15,35+$X1B);
$pdf->Cell(180, 4, utf8_decode("En PTO.CABELLO a los ").strtoupper($DIA).utf8_decode(" días del mes de ").strtoupper(nombremes($m))." de ".strtoupper($AÑO), 0, 0, 'C', 0);


$X1B=60;
$L=20;
$pdf->Line(35-$L, 200+$X1B, 90-$L, 200+$X1B);

$pdf->SetFont('Arial','',10);
$pdf->SetXY(35-$L,200+$X1B);
$pdf->Cell(180, 5, utf8_decode("MSc. ALIRIO SANCHEZ FLORE"), 0, 0, 'L', 0);

$pdf->SetXY(54-$L,205+$X1B);
$pdf->Cell(180, 5, utf8_decode("Director"), 0, 0, 'L', 0);

$pdf->SetXY(40-$L,210+$X1B);
$pdf->Cell(180, 5, utf8_decode("del I.U.T. Puerto Cabello"), 0, 0, 'L', 0);



$L2=52;
$pdf->Line(27+$L2, 200+$X1B, 82+$L2, 200+$X1B);
$pdf->SetXY(32+$L2,200+$X1B);
$pdf->Cell(180, 5, utf8_decode("ESP. DIONI RODRIGUEZ"), 0, 0, 'L', 0);

$pdf->SetXY(33+$L2,205+$X1B);
$pdf->Cell(180, 5, utf8_decode("Sub Director(a) Académico"), 0, 0, 'L', 0);

$pdf->SetXY(35+$L2,210+$X1B);
$pdf->Cell(180, 5, utf8_decode("del I.U.T. Puerto Cabello"), 0, 0, 'L', 0);


$L2=115;
$pdf->Line(27+$L2, 200+$X1B, 82+$L2, 200+$X1B);
$pdf->SetXY(32+$L2,200+$X1B);
$pdf->Cell(180, 5, utf8_decode("MSc. JUSTIBER IBARRA"), 0, 0, 'L', 0);

$pdf->SetXY(33+$L2,205+$X1B);
$pdf->Cell(180, 5, utf8_decode("Jefa de Control de Estudios"), 0, 0, 'L', 0);

$pdf->SetXY(35+$L2,210+$X1B);
$pdf->Cell(180, 5, utf8_decode("del I.U.T. Puerto Cabello"), 0, 0, 'L', 0);





	$pdf->Piedepagina($X1);
	$pdf->Output();



?>
