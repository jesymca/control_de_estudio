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
require('db.php');
$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
$id = $_POST['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Eliminar";

$id = intval($_POST['id']);
$query = "SELECT * FROM agregarseccion WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$pensum=$row["pensum"];
$cod_mat=$row["cod_mat"];
$seccion=$row["seccion"];
$cod_doc=$row["cod_doc"];
$lapso=$row["lapso"];
$aula=$row["aula"];
$descrip=$row["descrip"];
$hora=$row["hora"];
$tipo=$row["tipo"];
$electiva=$row["electiva"];
}
$sql = "DELETE FROM agregarseccion WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
