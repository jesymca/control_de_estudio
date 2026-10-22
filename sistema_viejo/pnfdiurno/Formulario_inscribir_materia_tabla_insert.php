<?php


session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
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

$alumno = $_POST['alumno'];
$seccion = $_POST['seccion'];


$sql = "INSERT INTO inscribir_materia(alumno,seccion)
VALUES ('$alumno','$seccion')";

if ($conn->query($sql) === TRUE) {
 echo "registro creado";
} else {
 echo $conn->error;
}

$conn->close();

?>
