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
date_default_timezone_set('America/Caracas');

$pensum = $_POST['pensum'];
$descrip2 = $_POST['descrip2'];
$creditos = $_POST['creditos'];
$aprobatori = $_POST['aprobatori'];
$cod_mat = $_POST['cod_mat'];

$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Editarsec";

$cod=$_SESSION['id'];
$usuario=$_SESSION['username'];

                                     

$sql = "INSERT INTO lismat_auditoria (accion,cod,usuario,pensum,cod_comp,cod_mat,descrip2,creditos,aprobatori,hora,fecha) VALUES ('$accion','$cod,'$usuario','$pensum','$cod_comp','$cod_mat','$descrip2','$creditos','$aprobatori',hora','$fecha')";

if ($conn->query($sql) === TRUE) { 
} 

$conn->close();

echo "listo";

?>
