<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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


$id = $_POST['id'];
$accion=$_POST['accion'];
$usuario=$_POST['usuario'];
$cedula=$_POST['cedula'];
$cod_mat=$_POST['cod_mat'];
$cod_mat_ant=$_POST['cod_mat_ant'];
$carrera=$_POST['carrera'];
$carrera_ant=$_POST['carrera_ant'];
$nota=$_POST['nota'];
$nota_ant=$_POST['nota_ant'];
$lapso=$_POST['lapso'];
$lapso_ant=$_POST['lapso_ant'];
$tiplap=$_POST['tiplap'];
$tiplap_ant=$_POST['tiplap_ant'];
$cod_doc=$_POST['cod_doc'];
$cod_doc_ant=$_POST['cod_doc_ant'];
$cod_usu=$_SESSION['username'];
$cod_usu_ant=$_POST['cod_usu_ant'];
$acu=$_POST['acu'];
$acu_ant=$_POST['acu_ant'];
$seccion=$_POST['seccion'];
$seccion_ant=$_POST['seccion_ant'];
$electiva=$_POST['electiva'];
$electiva_ant=$_POST['electiva_ant'];


include('/Classes/class_api.php');
$x=new PDF();
$x->notas_modificar($cedula,$cod_mat,$nota,$lapso,$cod_doc,$cod_usu,$acu,$seccion,$electiva,$id);
?>
