<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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

$cedula = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$electiva = $_POST['electiva'];
$nota = $_POST['nota'];
$lapso = $_POST['lapso'];
$cod_doc = $_POST['cod_doc'];
$acu = $_POST['acu'];
$seccion=$_POST['seccion'];

include('/Classes/class_api.php');
$x=new PDF();

$x->notas_insertar($cedula,$cod_mat,$nota,$lapso,$cod_doc,$acu,$seccion,$electiva);
$x->agregarseccion_insertar($cod_mat, $seccion, $cod_doc, $lapso, $electiva);

?>
