<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['docente']==1) {
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
$query = "SELECT * FROM docente WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$cod_doc=$row["cod_doc"];
$cedula=$row["cedula"];
$nombre=$row["nombre"];
$condicion=$row["condicion"];
$depart=$row["depart"];
$sexo=$row["sexo"];
$fechanac=$row["fechanac"];
$titulo_c=$row["titulo_c"];
$titulo_l=$row["titulo_l"];
$tipo=$row["tipo"];
$ingreso=$row["ingreso"];
$categoria=$row["categoria"];
$dedicacion=$row["dedicacion"];
$telefono=$row["telefono"];
$asignatura=$row["asignatura"];
$horas_ad=$row["horas_ad"];
$horas_do=$row["horas_do"];
$observa=$row["observa"];
$actividad=$row["actividad"];
$turno=$row["turno"];
}
$sql = "INSERT INTO docente_auditoria (accion,cod,usuario,cod_doc,cedula,nombre,condicion,depart,sexo,fechanac,titulo_c,titulo_l,tipo,ingreso,categoria,dedicacion,telefono,asignatura,horas_ad,horas_do,observa,actividad,turno,hora,fecha) VALUES ('$accion','$cod','$usuario','$cod_doc','$cedula','$nombre','$condicion','$depart','$sexo','$fechanac','$titulo_c','$titulo_l','$tipo','$ingreso','$categoria','$dedicacion','$telefono','$asignatura','$horas_ad','$horas_do','$observa','$actividad','$turno','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "DELETE FROM docente WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
