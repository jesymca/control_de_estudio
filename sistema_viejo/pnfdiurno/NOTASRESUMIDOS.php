<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
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
auditar("PENSA DE ESTUDIO",$_POST["SECCION"]." - ".$_POST["LAPSO"]);

class PDF extends FPDF
{

	function Encabezado()
	{
		$this->AddPage();
		date_default_timezone_set('America/Caracas');
		
		$DIA=date("d");
		$MES=date("m");
		$AÑO=date("Y");
		
		$hoy = date("d-m-Y");        

	
		
		$id = intval($_GET['id']);
		if ($id=="") {
			$id=$_SESSION['id'];
		}

		include "db.php";

		$sql = "SELECT * FROM alumno WHERE id='".$id."'";
		$resultado = $conn->query($sql);
		while($fila = $resultado->fetch_assoc()) {                      
			$CEDULA=$fila["cedula"];			
		}  		

		$sql = "SELECT * FROM alumno WHERE cedula='".$CEDULA."'";
		$resultado = $conn->query($sql);

		if (!$resultado) {	
			exit;
		}

		while($fila = $resultado->fetch_assoc()) { 

			$CODIGO=$fila['codigo'];  
			$CEDULA=$fila['cedula']; 	    
			$carrera=$fila['carrera'];
			$MENCION=$fila['mencion'];
			$PLAN=$fila['plan'];
			$NOMBRE=$fila['nombre'];
			$SEMESTRE=$fila['semestre'];
			$ACTIVIDAD=$fila['actividad'];
			$NIVEL=$fila['nivel'];
		}

		$PENSUM=$carrera.$MENCION.$PLAN;


include('getpensum3_clase.php');
$con = new carreras();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);


        //$this->SetFillColor(0,0,0);
		$this->SetFont('Arial','',8);


		$X1=5;
		//$this->Image("logoiutpc.jpg" , 15 ,0+$X1, 35 , 25 , "jpg" ,"");

		$this->SetXY(15,0+$X1);
		$this->Cell(190, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'C', 0);
		$this->SetXY(15,4+$X1);
		$this->Cell(190, 5, utf8_decode("PUERTO CABELLO"), 0, 0, 'C', 0);
		
		$this->SetXY(15,4+$X1);
		$this->Cell(185, 5, $hoy, 0, 0, 'R', 0);
		
		$this->SetXY(15,8+$X1);
		$this->Cell(190, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'C', 0);

		$this->SetFont('Arial','B',10);
		$this->SetXY(15,15+$X1);
		$this->Cell(190, 5, utf8_decode("NOTAS RESUMIDAS"), 0, 0, 'C', 0);
		
		$this->SetFont('Arial','',8);
		$this->SetXY(15,20+$X1);
		$this->Cell(20, 7, "CODIGO: ".$CODIGO, 0, 0, 'L', 0);

		$this->SetXY(50,20+$X1);
		$this->Cell(15, 7, "CEDULA: ".$CEDULA, 0, 0, 'L', 0);

		$this->SetXY(85,20+$X1);
		$this->Cell(50, 7, "NOMBRE: ".utf8_decode($NOMBRE), 0, 0, 'L', 0);

		$this->SetXY(160,20+$X1);
		$this->Cell(15, 7, "ACT.: ".$ACTIVIDAD, 0, 0, 'L', 0);
		
		$this->SetXY(180,20+$X1);
		$this->Cell(15, 7, "SEMESTRE: ".$SEMESTRE, 0, 0, 'L', 0);
		
		$this->SetXY(160,26+$X1);
		$this->Cell(15, 7, "PLAN: ".$PLAN, 0, 0, 'L', 0);
		
		$this->SetXY(180,26+$X1);
		$this->Cell(15, 7, "NIVEL: ".$NIVEL, 0, 0, 'L', 0); 
		
		$this->SetXY(15,26+$X1);
		$this->Cell(190, 5, utf8_decode("CARRERA: ".$carrera_a1), 0, 0, 'L', 0);
		
		$this->SetXY(15,30+$X1);
		$this->Cell(190, 5, utf8_decode("ASIGNATURAS CURSADAS"), 0, 0, 'C', 0);
		
		$this->SetFont('Arial','B',8);
		$this->SetXY(15,35+$X1);
		$this->Cell(20, 7, utf8_decode("CODIGO"), 1, 1, 'C', 0);

		$this->SetXY(35,35+$X1);
		$this->Cell(75, 7, utf8_decode("NOMBRE DE LA ASIGNATURA"), 1, 1, 'C', 0);

		$this->SetXY(110,35+$X1);
		$this->Cell(10, 7,"SEM", 1, 1, 'C', 0);        
		$this->SetXY(120,35+$X1);        
		$this->Cell(10, 7, "UC", 1, 1, 'C', 0);
		$this->SetXY(130,35+$X1);
		$this->Cell(10, 7, "VC", 1, 1, 'C', 0);
		$this->SetXY(140,35+$X1);
		$this->Cell(15, 7, "NOTAS", 1, 1, 'C', 0);

		$this->SetXY(155,35+$X1);
		$this->Cell(15, 7, "LAPSO", 1, 1, 'C', 0);

		$this->SetXY(170,35+$X1);
		$this->Cell(15, 7, "TIPO", 1, 1, 'C', 0);
		
		$this->SetXY(185,35+$X1);
		$this->Cell(15, 7, "CUR.", 1, 1, 'C', 0);


	    //$this->SetFont('Arial','',8);
		//$this->SetXY(15,8+$X1);
		//$this->Cell(180, 5, utf8_decode("07/07/2017"), 0, 0, 'R', 0);
		$X1=5;
	}	
}


$pdf=new PDF();
$pdf->Encabezado();        

$X1B=12;
$X0=0;


include "db.php";
$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql);
while($fila = $resultado->fetch_assoc()) { 
                   
	$CEDULA=$fila["cedula"];
	
} 

$sql = "SELECT * FROM alumno WHERE cedula='".$CEDULA."'";
$resultado = $conn->query($sql);

$CREDSUM=0;

while($fila = $resultado->fetch_assoc()) {  

	$CODIGO=$fila['codigo'];			
	$carrera=$fila['carrera'];
	$MENCION=$fila['mencion'];
	$PLAN=$fila['plan'];
	
}

$pensum=$carrera.$MENCION.$PLAN;

$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' GROUP BY id ASC";
$resultado = $conn->query($sql);

$x1=0;
$APROBADOS=0;
$FALTANTES=0;

while($fila = $resultado->fetch_assoc()) {  
	
if(substr($fila['cod_mat'], 3, 1)=="R"){
	$lismat_cod_mat[$x1]=$fila['cod_mat'];			
	$descrip2[$x1]=$fila['descrip2'];
	$creditos[$x1]=$fila['creditos'];
	$x1=$x1+1;	       
	}   
	              
}




$sql = "SELECT * FROM notas WHERE codigo='".$CODIGO."'  GROUP BY cod_mat ASC";
$resultado = $conn->query($sql);

$x1=0;

while($fila = $resultado->fetch_assoc()) {  
	if(substr($fila['cod_mat'], 3, 1)=="R"){
	$cod_mat[$x1]=$fila['cod_mat'];			
	$nota[$x1]=$fila['nota'];
	$lapso[$x1]=$fila['lapso'];
	$tiplap[$x1]=$fila['tiplap'];

	// echo $cod_mat[$x1]."<br>";
 //    echo $nota[$x1]."<br>";
 //    echo $lapso[$x1]."<br>";
 //    echo $tiplap[$x1]."<br>"; 
	
	$x1=$x1+1;  
} 
                    
}





$sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
$resultado = $conn->query($sql);

$CREDSUM=0;

while($fila = $resultado->fetch_assoc()) { 
	$NOMBRE_USER=$fila['nombre'];              
}



for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 

         // echo $lismat_cod_mat[$i]."<br>";
//}

	$x=0;

	
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(15,35+$X1B);
	if($cod_mat[$i]<>""){
		$pdf->Cell(20, 4,$cod_mat[$i], 1, 1, 'C', 0);
	}else{
		$pdf->Cell(20, 4,$lismat_cod_mat[$i], 1, 1, 'C', 0);	
	}
	$pdf->SetXY(35,35+$X1B);
	$pdf->Cell(75, 4,utf8_decode($descrip2[$i]), 1, 1, 'C', 0);

	$pdf->SetXY(110,35+$X1B);
	$pdf->Cell(10, 4,abs(substr($lismat_cod_mat[$i],1,2)), 1, 1, 'C', 0);        
	$pdf->SetXY(120,35+$X1B);             
	$pdf->Cell(10, 4, $creditos[$i], 1, 1, 'C', 0);
	
	$pdf->SetXY(130,35+$X1B);
	$pdf->Cell(10, 4, 0, 1, 1, 'C', 0);
	
	$pdf->SetXY(140,35+$X1B);
	$pdf->Cell(15, 4, $nota[$i], 1, 1, 'C', 0);
	
	$pdf->SetXY(155,35+$X1B);
	$pdf->Cell(15, 4, $lapso[$i], 1, 1, 'C', 0);

	$pdf->SetXY(170,35+$X1B);
	$pdf->Cell(15, 4, $tiplap[$i], 1, 1, 'C', 0);

	$pdf->SetXY(185,35+$X1B);
	if($nota[$i]<12){
		$pdf->Cell(15, 4, "SI", 1, 1, 'C', 0);
		$APROBADOS=$APROBADOS+1;
	}else{
		$pdf->Cell(15, 4, "", 1, 1, 'C', 0);
		$FALTANTES=$FALTANTES+1;
	}

	$MAX_A_CURSAR=$APROBADOS+$FALTANTES;

	if($nota[$i]>0){
            //$nota_sum=$nota_sum+$nota[$i]/$n1;
            //$n1=$n1+1;
	}else{
	}	
	

	$x=$x+1;

	$CREDSUM=$CREDSUM+$creditos[$i];
	$X1B=$X1B+4;
	$X0=$X0+1;


	if($X0>56){

		$pdf->Encabezado();
		$X1B=12;
		$X0=0;
	}

	
	
}

$pdf->SetFont('Arial','',8);
$pdf->SetXY(15,37+$X1B);
$pdf->Cell(50, 7, "RESUMEN:", 0, 0, 'L', 0);
$pdf->SetXY(120,37+$X1B);
$pdf->Cell(50, 7, "Emitido por: ".$NOMBRE_USER, 0, 0, 'L', 0);


$pdf->SetXY(15,42+$X1B);
$pdf->Cell(50, 7, "Indice de Rendimiento Academico: ", 0, 0, 'L', 0);

$pdf->SetXY(100,42+$X1B);
$pdf->Cell(50, 7, "GENERAL: ", 0, 0, 'L', 0);

$pdf->SetXY(130,42+$X1B);
$pdf->Cell(50, 7, "APROBADOS: ".$APROBADOS, 0, 0, 'L', 0);


$pdf->SetXY(160,42+$X1B);
$pdf->Cell(50, 7, "FALTANTES: ".$FALTANTES, 0, 0, 'L', 0);

$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.001);
$pdf->Line(15, 43+$X1B, 200,43+$X1B);


$pdf->SetXY(100,47+$X1B);
$pdf->Cell(50, 7, "EQUIVALENTES: ", 0, 0, 'L', 0);

$pdf->SetXY(130,47+$X1B);
$pdf->Cell(50, 7, "MAX. A CURSAR: ".$MAX_A_CURSAR, 0, 0, 'L', 0);


$pdf->SetXY(30,47+$X1B);
$pdf->Cell(50, 7, $nota_sum, 0, 0, 'L', 0);

$pdf->Line(15, 53+$X1B, 200,53+$X1B);


$pdf->SetXY(15,52+$X1B);
$pdf->Cell(50, 7, "A= Acreditado", 0, 0, 'L', 0);

$pdf->Output();


?>
