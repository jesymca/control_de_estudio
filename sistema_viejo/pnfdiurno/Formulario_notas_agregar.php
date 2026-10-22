<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas']==1) {
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

$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Agregar";
$id=$_POST["id"];
$codigo=$_POST["codigo"];
$cod_mat=$_POST["cod_mat"];
$carrera=$_POST["carrera"];
$nota="0";
$lapso=$_POST["lapso"];
$tiplap=$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];
$cod_usu=$_POST["cod_usu"];
$acu="0";
$seccion=$_POST["seccion"];

include('/Classes/class_api.php');
$pdf=new PDF();
$pdf->INSERT_INTO_notas($codigo,$cod_mat,$carrera,$nota,$lapso,$tiplap,$cod_doc,$cod_usu,$acu,$seccion);

?>
