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

$id = $_POST['id'];
$sql = "DELETE FROM user WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
echo "registro Borrado";
} else {
echo "Error al Borrar el registro: ".$conn->error;
}

$conn->close();


?>
