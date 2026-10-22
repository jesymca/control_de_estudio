<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['electivas']==1) {
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
$accion="Eliminar";

$id = intval($_POST['id']);
$query = "SELECT * FROM electivas WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$cod_ele=$row["cod_ele"];
$descrip2=$row["descrip2"];
$pensum=$row["pensum"];
}
$sql = "INSERT INTO electivas_auditoria (accion,cod,usuario,hora,fecha,electiva,cod_ele,descrip2,pensum) VALUES ('$accion','$cod','$usuario','$hora','$fecha','$electiva','$cod_ele','$descrip2','$pensum');";
$result = mysqli_query($conn, $sql);

$sql = "DELETE FROM electivas WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
