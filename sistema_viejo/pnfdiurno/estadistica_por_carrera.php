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
         
$carrera=substr($_POST['pensum'], 0, 1);

$carrera_a2=$pdf->carrera_larga($carrera);

$pdf->estadistica_de_carga_de_notas_por_carrera_encabezado($carrera_a2);

$pdf->estadistica_de_carga_de_notas_por_carrera_contenido($carrera);

// $pdf->estadistica_de_carga_de_notas_encabezado();

// $pdf->estadistica_de_carga_de_notas_contenido();


$pdf->Output();

?>
