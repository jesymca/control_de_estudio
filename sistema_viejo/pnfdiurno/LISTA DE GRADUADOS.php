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




$fe_gr_alu = $_POST['fe_gr_alu'];
include('/Classes/class_api.php');
$pdf=new PDF(L);
$pdf->datos_graduados($fe_gr_alu);



?>
