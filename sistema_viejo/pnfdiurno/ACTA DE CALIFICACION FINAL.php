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

require ("aud.php");
auditar("ACTAS_FINAL",$_POST["cod_mat"]." - ".$_POST["LAPSO"]);

$lapso=$_POST["lapso"];
$cod_mat=$_POST["cod_mat"];        
$cod_doc=$_POST["cod_doc"];		
$seccion=$_POST["seccion"];	

$pdf->acta_De_calificacion_contenido($lapso,$cod_mat,$cod_doc,$seccion);


?>
