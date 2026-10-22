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

require('db.php');

$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$nota = $_POST['editval'];
$lapso = $_POST['lapso'];
$tiplap = $_POST['tiplap'];
$cod_doc = $_POST['cod_doc'];
$acu = 0;
$cod_usu=$_SESSION['username'];
$seccion=$_POST['seccion'];

date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Editarsec";
$cod=$_SESSION['id'];
$usuario=$_SESSION['username'];


$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

if ($conn->query($sql) === TRUE) { 
} 

$conn->close();



?>
