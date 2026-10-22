<?php

require('db.php');

$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$carrera = $_POST['carrera'];
$nota = $_POST['nota'];
$lapso = $_POST['lapso'];
$tiplap = $_POST['tiplap'];
$cod_doc = $_POST['cod_doc'];
$cod_usu = $_POST['cod_usu'];
$acu = $_POST['acu'];
$seccion = $_POST['seccion'];

$sql = "INSERT INTO notas(codigo,cod_mat,carrera,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion)
    VALUES ('$codigo','$cod_mat','$carrera','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion')";

if ($conn->query($sql) === TRUE) {
header("Location: Formulario_aula_tabla_index.php");
} else {
 echo $conn->error;
}

$conn->close();

?>
