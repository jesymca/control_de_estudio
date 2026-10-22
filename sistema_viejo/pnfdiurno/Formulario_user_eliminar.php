<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user']==1) {
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
$query = "SELECT * FROM user WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{ 
$id=$row["id"];
$nombre=$row["nombre"];
$login=$row["login"];
$clave=$row["clave"];
$alumno=$row["alumno"];
$docente=$row["docente"];
$notas=$row["notas"];
$notas_guardar=$row["notas_guardar"];
$notas_modificar=$row["notas_modificar"];
$notas_borrar=$row["notas_borrar"];
$lapso=$row["lapso"];
$lismat=$row["lismat"];
$seccion=$row["seccion"];
$tipos_lapso=$row["tipos_lapso"];
$nota=$row["nota"];
$user=$row["user"];
$user_clave=$row["user_clave"];
$auditoria=$row["auditoria"];
$actas=$row["actas"];
$historiales=$row["historiales"];
$agregar_seccion=$row["agregar_seccion"];
$inscribir_materia=$row["inscribir_materia"];
$copiar_seccion=$row["copiar_seccion"];
$eliminar_seccion=$row["eliminar_seccion"];
$horas=$row["horas"];
$aula=$row["aula"];
$electivas=$row["electivas"];
$cambiar_docente=$row["cambiar_docente"];
$cambiar_lapso=$row["cambiar_lapso"];
$cambiar_seccion=$row["cambiar_seccion"];
$cambiar_materia=$row["cambiar_materia"];
$desactivar_alumnos=$row["desactivar_alumnos"];
}
$sql = "DELETE FROM user WHERE  id='".$_POST['id']."'";

if ($conn->query($sql) === TRUE) {
  echo "registro Borrado";
} else {
  echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();
?>
