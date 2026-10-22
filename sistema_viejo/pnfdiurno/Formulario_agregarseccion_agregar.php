<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['agregar_seccion']==1) {
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
$pensum=$_POST["pensum"];
$cod_mat=$_POST["cod_mat"];
$seccion=$_POST["seccion"];
$cod_doc=$_POST["cod_doc"];
$lapso=$_POST["lapso"];
$electiva=$_POST["electiva"];
$cod_usu=$_SESSION['username'];
include('/Classes/class_api.php');
$x=new PDF();

$x->agregarseccion_insertar($cod_usu, $cod_mat, $seccion, $cod_doc, $lapso, $electiva);

?>
