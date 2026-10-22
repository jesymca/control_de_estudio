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
require("db.php");
//include('validacion.php');
$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$nota = $_POST['nota'];
$lapso = $_POST['lapso'];

if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
	$tiplap=substr($cod_mat, 1, 1);
}else{
	$tiplap="";
}



$cod_doc = $_POST['cod_doc'];
$acu = $_POST['acu'];
$id = $_POST['id'];

$seccion = $_POST['seccion'];

$cod_usu=$_SESSION['username'];


$sql = "UPDATE notas SET  codigo = '$codigo' , cod_mat = '$cod_mat' , nota = '$nota' , lapso = '$lapso' , tiplap = '$tiplap' , cod_doc = '$cod_doc' , cod_usu = '$cod_usu' ,acu = '$acu',seccion = '$seccion' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
echo "registro actualizado";
} else {
echo "Error al actualizar el registro ";
}

date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Modificar";
$cod=$_SESSION['id'];
$usuario=$_SESSION['username'];

$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

$result = $conn->query($sql);


$conn->close();

header("Location: Formulario_notas_tabla_index2.php");


?>
