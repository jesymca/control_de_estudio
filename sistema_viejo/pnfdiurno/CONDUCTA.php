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

$pensum=$_POST["pensum"];
$cedula=$_POST["cedula"];
$grado=$_POST["grado"];

include('/Classes/class_api.php');
$pdf=new PDF();

$pdf->conducta($pensum,$cedula,$grado);
?>
