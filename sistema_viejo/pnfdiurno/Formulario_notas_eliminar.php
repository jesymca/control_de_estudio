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
require('db.php');
$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
$id = $_POST['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Eliminar_inc";

$id = intval($_POST['id']);
$query = "SELECT * FROM notas WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$codigo=$row["codigo"];
$cod_mat=$row["cod_mat"];
$carrera=$row["carrera"];
$nota=$row["nota"];
$lapso=$row["lapso"];
$tiplap=$row["tiplap"];
$cod_doc=$row["cod_doc"];
$cod_usu=$row["cod_usu"];
$acu=$row["acu"];
$seccion=$row["seccion"];
}
$sql = "INSERT INTO notas_auditoria (accion,cod,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,hora,fecha) VALUES ('$accion','$cod','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "DELETE FROM notas WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
