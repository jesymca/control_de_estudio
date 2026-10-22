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

 $pdf=new PDF($orientation='L');   // Se agrega la orientacion en horizontal 	: 08-11-2023  Ing. Elio Milano

 



$X0_3=1;

$carrera=$_POST["carrera"];	 
       
$carrera_a1=$pdf->carrera_larga($carrera);

$pdf->alumnos_activos_Encabezado($X0_3);	


include('db.php');    
$sql = "SELECT cedula,nombre,,carrera,actividad,sexo,tipingreso FROM alumno WHERE actividad=1 ORDER BY cedula, tipingreso ASC";// 
$resultado = $conn->query($sql);

$i=1;
$i2=1;
$X1=-10;
$X0_2=0;

$X0=0;

if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {


$carrera_a2=$pdf->carrera_corta($fila["carrera"]);

		// switch ($fila["carrera"])  {
		// 	case "M":
		// 	$carrera_a1="P.N.F. MECANICA";
		// 	$carrera_a2="MECANICA";
		// 	break;
		// 	case "T":
		// 	$carrera_a1="P.N.F. MANTENIMIENTO";
		// 	$carrera_a2="MANTENIMIENTO";
		// 	break;
		// 	case "E":
		// 	$carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
		// 	$carrera_a2="MATERIALES";
		// 	break;
		// 	case "I":
		// 	$carrera_a1="P.N.F. INFORMATICA";
		// 	$carrera_a2="INFORMATICA";
		// 	break;
		// 	case "G":
		// 	$carrera_a1="P.N.F. TURISMO";
		// 	$carrera_a2="TURISMO";
		// 	break;   
		// 	case "O":
		// 	$carrera_a1="P.I.F. MECANICA TERMICA";
		// 	$carrera_a2="TERMICA";
		// 	break;
		// 	case "A":
		// 	$carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
		// 	$carrera_a2="AUTOMOTRIZ";
		// 	break;
		// }


        $pdf->SetFont('Arial','',7);

 // esta linea controla la posicion (X,Y) 
		$pdf->SetXY(15,65+$X1);					
// esta linea controla la posicion en de la celda (Ancho y Alto)
		$pdf->Cell(8, 5, $i, 1, 1, 'C', 0);

// esta linea controla la posicion (X,Y) 
		$pdf->SetXY(23,65+$X1);
// esta linea controla la posicion en de la celda (Ancho y Alto)
		$pdf->Cell(15, 5, $fila["cedula"], 1, 1, 'C', 0);


// esta linea controla la posicion (X,Y) 
		$pdf->SetXY(38,65+$X1);
// esta linea controla la posicion en de la celda (Ancho y Alto)
		$pdf->Cell(70, 5, utf8_decode(strtoupper($fila["nombre"])), 1, 1, 'C', 0);

// esta linea controla la posicion (X,Y) 
		$pdf->SetXY(108,65+$X1);
// esta linea controla la posicion en de la celda (Ancho y Alto)		
		$pdf->Cell(30, 5, utf8_decode($carrera_a2), 1, 1, 'C', 0);

// esta linea controla la posicion (X,Y) 
		$pdf->SetXY(138,65+$X1);
// esta linea controla la posicion en de la celda (Ancho y Alto)		
		$pdf->Cell(20, 5, $fila["actividad"], 1, 1, 'C', 0);


		$X1=$X1+5;
// para pruebas del campo sexo

// esta linea controla la posicion (X,Y)		
		$pdf->SetXY(158,65+$X1);
// esta linea controla la posicion en de la celda (Ancho y Alto)		
		$pdf->Cell(10, 5, $fila["sexo"], 1, 1, 'C', 0);
		
// fin prueba		

// para pruebas del campo TIPINGRESO
		$pdf->SetXY(168,65+$X1);
		$pdf->Cell(30, 5, $fila["tipingreso"], 1, 1, 'C', 0);
		
// fin prueba	


		$X0=$X0+1;
		$X0_2=$X0_2+1;
		$X1B=$X1B+5;

		$i++; 
		$i2++;


		if($X0_2>18){   // establece el nro de lineas en el detalle del reporte
        
        $X0_3=$X0_3+1;

        

			$pdf->alumnos_activos_Encabezado($X0_3);
			$X1=-10;
			$X0_2=0;

		}

	}
}


$pdf->Output();


?>
