<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
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
$query = "SELECT * FROM alumno WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$cedula=$row["cedula"];
$nombre=$row["nombre"];
$marca=$row["marca"];
$fe_gr_alu=$row["fe_gr_alu"];
}
$sql = "INSERT INTO alumno_auditoria (accion,cod,usuario,codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,ingreso,semestre,egreso,pasantia,turno,trabajo,beca,ireceptor,folio,tomo,rusnies,discapacid,pnf,trayecto,hora,fecha) VALUES ('$accion','$cod','$usuario','$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$lugar','$municipio','$estado','$procedenci','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$tipingreso','$ingreso','$semestre','$egreso','$pasantia','$turno','$trabajo','$beca','$ireceptor','$folio','$tomo','$rusnies','$discapacid','$pnf','$trayecto','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "DELETE FROM alumno WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
