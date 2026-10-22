<?php

require ('fpdf.php'); 
require ('num2letras.php');

class acta_De_calificacion extends FPDF
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
		
		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"];        
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];		
		$carrera=substr($MATERIA, 0, 1);

		if(substr($MATERIA, 1, 1)=="P" OR substr($MATERIA, 1, 1)=="T" OR substr($MATERIA, 1, 1)=="E"){
			$TIPO=substr($MATERIA, 1, 1);
		}else{
			$TIPO="";
		}

		include('getpensum3.php');         
		$carrera_a1=carrera_larga($carrera);
		$carrera_a2=carrera_corta($carrera);
       
		$this->SetFont('Arial','B',12);
		$X1=5;
		$this->Image("LOGO.jpg" , 10 ,0+$X1, 43 , 25 , "jpg" ,"");

		$this->SetXY(54,0+$X1);
		$this->Cell(136, 5, utf8_decode("REPÚBLICA BOLIVARIANA DE VENEZUELA"), 0, 0, 'L', 0);
		$this->SetFont('Arial','',10);
		$this->SetXY(54,4+$X1);
		$this->Cell(136, 5, utf8_decode("MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA"), 0, 0, 'L', 0);
		$this->SetXY(54,8+$X1);
		$this->Cell(136, 5, utf8_decode("CIENCIA Y TECNOLOGÍA"), 0, 0, 'L', 0);

		$this->SetXY(54,15+$X1);
		$this->Cell(136, 5, utf8_decode("UNIVERSIDAD POLITÉCNICA TERRITORIAL"), 0, 0, 'L', 0);
		
		include "db.php";

		$sql = "SELECT * FROM sede where id=1";
		$resultado = $conn->query($sql);
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {                      
				$sede  = $fila['lugar'];					
			}  
		}	

		$this->SetXY(54,19+$X1);
		$this->Cell(136, 5, utf8_decode($sede), 0, 0, 'L', 0);
		$this->SetXY(54,23+$X1);
		$this->Cell(136, 5, utf8_decode("DEPARTAMENTO DE CONTROL DE ESTUDIOS"), 0, 0, 'L', 0);

		$this->SetFont('Arial','B',10);		
		$this->SetXY(55,35+$X1);
		$this->Cell(100, 7,"ACTA DE CALIFICACION FINAL ".$LAPSO." ".$TIPO, 1, 1, 'C', 0);


		$this->SetFont('Arial','',8);
		$this->SetXY(15,45+$X1);
		$this->Cell(136, 5, utf8_decode("ASIGNATURA:"), 0, 0, 'L', 0);

		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"];        
		
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];	
		
		$COD=substr($MATERIA, 0, 5);
		
		$SECCION=$_POST["seccion"];
		$carrera=substr($MATERIA, 0, 1);

		if(substr($MATERIA, 1, 1)=="P" OR substr($MATERIA, 1, 1)=="T" OR substr($MATERIA, 1, 1)=="E"){
			$TIPO=substr($MATERIA, 1, 1);
		}else{
			$TIPO="";
		}
		
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
		$this->Cell(250, 5, $DESCRIP2." (".$COD.")", 0, 0, 'L', 0);

		$this->SetXY(130,45+$X1);
		$this->Cell(136, 5, utf8_decode("SECCIÓN: ").$semestre." - ".$SECCION, 0, 0, 'L', 0);

		$this->SetXY(170,45+$X1);
		$this->Cell(20, 5, utf8_decode("U.C.: ").$CRED, 0, 0, 'L', 0);


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

		$this->SetLineWidth(0.4);
		$this->Line(35,63,97,63);

		$this->Line(125,63,142,63);
		$this->SetLineWidth(0.2);
		$this->SetXY(110,53+$X1);
		$this->Cell(136, 5, utf8_decode("CÉDULA:"), 0, 0, 'L', 0);
		$this->SetXY(125,53+$X1);
		$this->Cell(136, 5, $CEDULA, 0, 0, 'L', 0);


		$this->SetXY(145,53+$X1);
		$this->Cell(136, 5, utf8_decode("DEPT.:"), 0, 0, 'L', 0);

		$this->SetXY(157,53+$X1);
		$this->Cell(136, 5, utf8_decode($carrera_a2), 0, 0, 'L', 0);

		$this->SetFont('Arial','B',8);

		$this->SetXY(15,60+$X1);
		$this->Cell(180, 10, "", 1, 1, 'L', 0);

		$this->SetXY(15,65+$X1);
		$this->Cell(136, 5, utf8_decode("NUM."), 0, 0, 'L', 0);

		$this->SetXY(23,65+$X1);
		$this->Cell(136, 5, utf8_decode("CODIGO"), 0, 0, 'L', 0);

		$this->SetXY(35,65+$X1);
		$this->Cell(136, 5, utf8_decode("CEDULA"), 0, 0, 'L', 0);

		$this->SetXY(55,65+$X1);
		$this->Cell(136, 5, utf8_decode("NOMBRE DEL ALUMNO"), 0, 0, 'L', 0);


		$this->SetXY(125,60+$X1);
		$this->Cell(70, 10, "", 1, 1, 'L', 0);

		$this->SetXY(125,60+$X1);
		$this->Cell(136, 5, utf8_decode("DEFINITIVA"), 0, 0, 'L', 0);

		$this->SetXY(125,65+$X1);
		$this->Cell(136, 5, utf8_decode("ACUM"), 0, 0, 'L', 0);

		$this->SetXY(140,65+$X1);
		$this->Cell(136, 5, utf8_decode("NOTA"), 0, 0, 'L', 0);


		$this->SetXY(155,65+$X1);
		$this->Cell(136, 5, utf8_decode("LETRA"), 0, 0, 'L', 0);

		$this->SetXY(172,65+$X1);
		$this->Cell(136, 5, utf8_decode("Observaciones"), 0, 0, 'L', 0);


	}

	function Piedepagina($X1B,$IN,$APRO,$REPR)
	{
		
		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"];        
		
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];		
		
		$COD=substr($MATERIA, 0, 5);		
		
		$carrera=substr($MATERIA, 0, 1);

		if(substr($MATERIA, 1, 1)=="P" OR substr($MATERIA, 1, 1)=="T" OR substr($MATERIA, 1, 1)=="E"){
			$TIPO=substr($MATERIA, 1, 1);
		}else{
			$TIPO="";
		}

		        
		$carrera_a1=carrera_larga($carrera);
		$carrera_a2=carrera_corta($carrera);
		

		
		
		$this->SetLineWidth(0.4);
		$X3=20;

		$this->SetXY(30,73+$X1B);
		$this->Cell(150, 5, "========================= FIN DEL ACTA =========================", 0, 0, 'C', 0);


		$this->SetXY(15,80+$X1B);
		$this->Cell(40, 5, "Conformes", 1, 1, 'L', 0);

		$this->SetXY(15,85+$X1B);
		$this->Cell(40, 5, "Prof. Materia", 1, 1, 'L', 0);
		$this->SetXY(15,90+$X1B);
		$this->Cell(40, 5, "Control Estudios", 1, 1, 'L', 0);
		$this->SetXY(15,95+$X1B);
		$this->Cell(40, 5, "Director", 1, 1, 'L', 0);

		$this->SetFont('Arial','B',8);
		$this->SetXY(57,80+$X1B);
		$this->Cell(20, 5, "Observaciones", 0,0, 'L', 0);

		$this->SetXY(55,80+$X1B);
		$this->Cell(100, 20, "", 1, 1, 'L', 0);

		$this->SetFont('Arial','',8);

		$this->SetXY(57,85+$X1B);
		$this->Cell(40, 5, $MATERIA." - ".$LAPSO." - ".$cod_doc." - ".$SECCION, 0, 0, 'L', 0);


		$this->SetXY(57,90+$X1B);
		$this->Cell(40, 5, $carrera_a1, 0, 0, 'L', 0);

		date_default_timezone_set('UTC');
		$hoy = date("d-m-Y");
		$this->SetXY(155,80+$X1B);
		$this->Cell(40, 5, "Fecha:            ".$hoy, 1, 1, 'L', 0);
		$this->SetXY(155,85+$X1B);
		$this->Cell(40, 5, "Aprobados:             ".$APRO, 1, 1, 'L', 0);
		$this->SetXY(155,90+$X1B);
		$this->Cell(40, 5, "Reprobados:           ".$REPR, 1, 1, 'L', 0);
		$this->SetXY(155,95+$X1B);
		$this->Cell(40, 5, "Inasistentes:           ".$IN, 1, 1, 'L', 0);


		$this->SetFont('Arial','B',8);
		$this->SetXY(15,100+$X1B);
		$this->Cell(40, 5, "ORIGINAL Y COPIA:", 0, 0, 'L', 0);

		$this->SetFont('Arial','',8);
		$this->SetXY(43,100+$X1B);
		$this->Cell(80, 5, "Departamento de Control de Estudios", 0, 0, 'L', 0);

		$this->SetFont('Arial','B',8);
		$this->SetXY(25,105+$X1B);
		$this->Cell(40, 5, "TRIPLICADO", 0, 0, 'L', 0);

		$this->SetFont('Arial','',8);
		$this->SetXY(43,105+$X1B);
		$this->Cell(40, 5, "Para ser Publicado", 0, 0, 'L', 0);

		include "db.php";
		$sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
		$resultado = $conn->query($sql);
		
		$CREDSUM=0;
		
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 
				$NOMBRE_USER=$fila['nombre'];              
			}
		}

		$this->SetXY(120,105+$X1B);
		$this->Cell(40, 5, "USUARIO: ".$NOMBRE_USER, 0, 0, 'L', 0);
		

	}
}
?>