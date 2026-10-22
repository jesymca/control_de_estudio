<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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
$cod2=$_SESSION['id'];
$usuario2=$_SESSION['username'];

$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');


include 'db.php';
$input = filter_input_array(INPUT_POST);
$cod_mat = $_POST["cod_mat"];
$codigo = $_POST["cedula"];
$nota = $_POST["nota"];
$lapso = $_POST["lapso"];
$tiplap = $_POST["tiplap"];
$cod_doc = $_POST["cod_doc"];
$cod_usu = $_POST["cod_usu"];



if($input["action"] === 'edit')
{


if($input["nota"]<>"00" and $input["nota"]=="0" OR $input["nota"]=="01" OR $input["nota"]=="02"  OR $input["nota"]=="03"  OR $input["nota"]=="04"  OR $input["nota"]=="05"  OR $input["nota"]=="06"  OR $input["nota"]=="07"  OR $input["nota"]=="08"  OR $input["nota"]=="09"  OR $input["nota"]=="10"  OR $input["nota"]=="11"  OR $input["nota"]=="12"  OR $input["nota"]=="13"  OR $input["nota"]=="14"  OR $input["nota"]=="15"  OR $input["nota"]=="16"  OR $input["nota"]=="17"  OR $input["nota"]=="18"  OR $input["nota"]=="19"  OR $input["nota"]=="20" OR $input["nota"]=="IN"){

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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

if($nota>0){
$total=$nota*5;
}else{
$total=0;
}
$acu = $total;

$accion="Modificar";


include 'db.php';

 $sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,acu) VALUES ('$accion','$cod2','$hora','$fecha','$usuario2','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$acu');";
 $result = mysqli_query($conn, $sql);

    $query = "UPDATE notas SET nota = '".$nota."',acu = '".$total."' WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
    
}
    
}
if($input["action"] === 'delete')
{

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_borrar']==1) {
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


$accion="Borrar";

 $sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,acu) VALUES ('$accion','$cod2','$hora','$fecha','$usuario2','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$acu');";
 $result = mysqli_query($conn, $sql);

    $query = "DELETE FROM notas WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
   

}
echo json_encode($input);
?>
