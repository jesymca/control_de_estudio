<?php

require('db.php');

$seccion = $_POST['seccion'];
$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$nota = $_POST['nota'];
$acum = $_POST['acum'];

$sql = "INSERT INTO cargar_notas_por_seccion(seccion,cedula,nombre,nota,acum)
VALUES ('$seccion','$cedula','$nombre','$nota','$acum')";

if ($conn->query($sql) === TRUE) {
 echo "registro creado";
} else {
 echo $conn->error;
}

$conn->close();

?>
