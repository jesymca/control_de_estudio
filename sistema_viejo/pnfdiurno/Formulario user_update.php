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

require ("Security.php");
require("db.php");

$nombre = $_POST['nombre'];
$login = $_POST['login'];
$clave = encriptar($_POST['clave']);
$id = $_POST['id'];

$sql = "UPDATE user SET  nombre = '$nombre' , login = '$login' , clave = '$clave' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
echo "registro actualizado";
} else {
echo "Error al actualizar el registro: ".$conn->error;
}

$conn->close();


?>
