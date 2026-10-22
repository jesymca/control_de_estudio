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
         
$pdf->estadistica_de_carga_de_notas_encabezado();
$pdf->estadistica_de_carga_de_notas_contenido();

$pdf->Output();

?>
