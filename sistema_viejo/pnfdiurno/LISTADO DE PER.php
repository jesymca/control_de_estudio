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
$pensum=$_POST["pensum"];
$cod_mat=$_POST["cod_mat"];
$lapso=$_POST["lapso"];  

$pdf=new PDF();

$datos_de_materias=$pdf->lista_de_materias($pensum,$grado,$cod_mat,$i);
list($lismat_cod_mat,$descrip2,$aprobatoria,$semestre,$trayecto,$divicion) = split('[|]', $datos_de_materias);

$pdf->listado_per_Encabezado($lapso,$cod_mat,$descrip2,$cod_doc,$trayecto);	
include "db.php";

$carrera=substr($cod_mat, 0, 1);
$cod_mat2="LIKE '%".substr($cod_mat, 2, 3)."%'";

$sql = "SELECT DISTINCT alumno.cedula,alumno.nombre,notas.seccion FROM alumno,notas WHERE alumno.carrera='".$carrera."' and notas.`cod_mat` ".$cod_mat2." and SUBSTRING(notas.lapso,1,4)='".$lapso."' and notas.codigo=alumno.cedula ORDER BY alumno.nombre";
$resultado = $conn->query($sql);

$X1A=-11;
$X0_2=0;
$X0=0;
$cantidad_de_notas=0;

$pdf->SetFont('Arial','',8);

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {   

		$datos_de_materias=$pdf->lista_de_materias($pensum,$grado,$cod_mat,$i);
		list($lismat_cod_mat,$descrip2,$aprobatoria,$semestre,$trayecto,$divicion) = split('[|]', $datos_de_materias);
		$resumida=$pdf->calcular_resumida_per($pensum,$X1B,$lismat_cod_mat,$fila['cedula'],$X1B,$descrip2,$semestre,$creditos,$trayecto,$aprobatori,$divicion,$cod_mat_libro_rector,$electiva,"false","",$cant,$apro,$lapso);
		list($nota_resumida, $lapso_resumida,$tipo_resumida,$aprobatori) = split('[|]', $resumida);

		if(abs($nota_resumida)>=6 and abs($nota_resumida)<$aprobatoria){
			
			$n=$n+1;
			$X0_2=$X0_2+1;
			$X1A=$X1A+4;
			
			$pdf->SetXY(15,67+$X1A);
			$pdf->Cell(9, 5, $n, 0, 0, 'C', 0);

			$pdf->SetXY(24,67+$X1A);		
			$pdf->Cell(22, 5, strtoupper($fila['cedula']), 0, 0, 'L', 0);

			$pdf->SetXY(46,67+$X1A);		
			$pdf->Cell(80, 5, strtoupper(substr(utf8_decode($fila['nombre']), 0, 50)), 0, 0, 'L', 0);

			$pdf->SetXY(146,67+$X1A);
			$pdf->Cell(5, 5,$nota_resumida, 0, 0, 'C', 0);

			$pdf->SetXY(158,67+$X1A);
			$pdf->Cell(5, 5,$fila['seccion'], 0, 0, 'C', 0);

			$pdf->SetXY(172,67+$X1A);
			$pdf->Cell(5, 5,$divicion, 0, 0, 'C', 0);
      		$nota_0=$pdf->verificar_carga_de_nota($fila['cedula'],$cod_mat,$lapso,$divicion);

			$pdf->SetXY(187,67+$X1A);
			$pdf->Cell(5, 5,$nota_0, 0, 0, 'C', 0);
		}

		if($X0_2>40){
			$pdf->listado_per_Encabezado($lapso,$cod_mat,$descrip2,$cod_doc,$trayecto);
			$X1A=-11;
			$X0_2=0;
		}
	}
}

$pdf->Output();

?>
