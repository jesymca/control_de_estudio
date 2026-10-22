<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['lismat']==1) {
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
$query = "SELECT * FROM lismat WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$pensum=$row["pensum"];
$cod_mat=$row["cod_mat"];
$descrip2=$row["descrip2"];
$creditos=$row["creditos"];
$aprobatori=$row["aprobatori"];
$semestre=$row["semestre"];
$trayecto=$row["trayecto"];
$divicion=$row["divicion"];
$nota=$row["nota"];
$cod_mat_libro_rector=$row["cod_mat_libro_rector"];
$cod_mat_ant=$row["cod_mat_ant"];
}
$sql = "INSERT INTO lismat_auditoria (accion,cod,usuario,pensum,cod_comp,cod_mat,descrip2,creditos,aprobatori,hora,fecha) VALUES ('$accion','$cod','$usuario','$pensum','$cod_comp','$cod_mat','$descrip2','$creditos','$aprobatori','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "DELETE FROM lismat WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
