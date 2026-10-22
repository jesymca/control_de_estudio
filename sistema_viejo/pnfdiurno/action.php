<?php  
//action.php
require("db.php");

$input = filter_input_array(INPUT_POST);


$cod_mat = mysqli_real_escape_string($conn, $input["cod_mat"]); 
$lapso = mysqli_real_escape_string($conn, $input["lapso"]); 
$tiplap = mysqli_real_escape_string($conn, $input["tiplap"]);       
$nota = mysqli_real_escape_string($conn, $input["nota"]);
$acu = mysqli_real_escape_string($conn, $input["acu"]);
$cod_doc = mysqli_real_escape_string($conn, $input["cod_doc"]);


if($input["action"] === 'edit')
{
 $query = "
 UPDATE notas 
 SET cod_mat = '".$cod_mat."', lapso = '".$lapso."' , tiplap = '".$tiplap."' , nota = '".$nota."' , acu = '".$acu."' , cod_doc = '".$cod_doc."' 
 WHERE id = '".$input["id"]."'";

 mysqli_query($conn, $query);

 require ("aud.php");
 auditar("notas_B",$input["id"]);

}
if($input["action"] === 'delete')
{
 $query = "
 DELETE FROM notas 
 WHERE id = '".$input["id"]."'";
 mysqli_query($conn, $query);

 require ("aud.php");
 auditar("notas_C",$input["id"]);

}

echo json_encode($input);

?>