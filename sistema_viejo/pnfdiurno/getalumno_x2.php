<?php
include('db.php');  

$cedula = $_POST['cedula'];
$cod_mat = $_POST['cod_mat'];

include('HISTORI.php');
$x=new PDF();


$resumida=$x->verificar_aprobada($cedula,$cod_mat,1);
list($nota_resumida, $lapso_resumida,$tipo_resumida,$aprobatori) = split('[|]', $resumida);

if ($nota_resumida<$aprobatori){
  echo "false";
}else{
	echo "true";
}



?>