
<?php
session_start();                                
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['cambiar_docente']==1) {
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

include "db.php";
$id = $_POST['id']; 
$cedula = $_POST['cedula'];           
$lapso = $_POST['lapso'];                 
$seccion = $_POST['seccion'];
$seccion2 = $_POST['seccion2'];




 /////////****************////////////////         

  $sql = "UPDATE notas SET `seccion` = '".$seccion2."' WHERE `codigo`='".$cedula."' AND `seccion`='".$seccion."' AND `lapso`='".$lapso."'";
  $conn->query($sql);

 /////////****************//////////////// 

   

$conn->close();    
header('Location: Formulario_notas_tabla_index.php?id='.$id);
?>


