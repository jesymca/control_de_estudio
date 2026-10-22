<?php
// session_start();
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
// } else {
//   header("Location: index.html");
//   exit;
// }
// $now = time();
//   if($now > $_SESSION['expire']) {
//   session_destroy();
//   echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
//   exit;
// }
require('db.php');
$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$tiplap = $_POST['tiplap'];
$cod_doc = $_POST['cod_doc'];
$seccion = $_POST['seccion'];
$carrera=substr($cod_mat, 0, 1);
$nota = "0";
$acu = 0;
$cod_usu=$_SESSION['username'];


// $codigo = "V11095251";
// $cod_mat = "1";
// $lapso = "1";
// $tiplap = "1";
// $cod_doc = 1;
// $seccion = "1";
// $carrera=substr($cod_mat, 0, 1);
// $nota = "0";
// $acu = 0;
// $cod_usu="WALTER";



$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera)
VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera')";

if ($conn->query($sql) === TRUE) {
 echo "registro creado";
} else {
 echo $conn->error;
}

// date_default_timezone_set('America/Caracas');
// $hora = strftime("%I:%M:%S %p\n");
// $fecha = date('d-m-Y');
// $accion="Guardar";
// $cod=$_SESSION['id'];
// $usuario=$_SESSION['username'];


// $sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

// if ($conn->query($sql) === TRUE) { 
// } 



// $conn->close();

// header("Location: Formulario_notas_tabla_index.php");



?>
