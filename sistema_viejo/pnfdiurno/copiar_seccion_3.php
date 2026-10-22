<?php


session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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

$accion="Guardar_copiar";
$usuario1=$_SESSION['username'];

require("db.php");


$pensum = $_POST['pensum'];
$cod_doc = $_POST['cod_doc'];
$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$seccion = $_POST['seccion'];

$cod_doc2 = $_POST['cod_doc2']; 
$cod_mat2 = $_POST['cod_mat2'];
$lapso2 = $_POST['lapso2'];
$seccion2 = $_POST['seccion2']; 

 if($_POST['Radios']=="A"){
   $connotas=0;
 }else{      
   $connotas=1;
 }

include('/Classes/class_api.php');
$pdf=new PDF();

include('db.php');

$pdf->guarda_seccion_copiar_0($pensum,$cod_mat,$cod_mat2,$cod_doc,$cod_doc2,$lapso,$lapso2,$seccion,$seccion2,$usuario1);
$pdf->mensaje_color("copiar_seccion_1.php","principal.php","Se copio la seccion",0,0);

?>


