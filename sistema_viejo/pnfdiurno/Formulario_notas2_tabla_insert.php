<?php

require('db.php');

$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$nota = $_POST['nota'];
$acu = $_POST['acu'];

$sql = "INSERT INTO notas2(cedula,nombre,nota,acu)
VALUES ('$cedula','$nombre','$nota','$acu')";

if ($conn->query($sql) === TRUE) {
 echo "registro creado";
} else {
 echo $conn->error;
}

$conn->close();

?>
