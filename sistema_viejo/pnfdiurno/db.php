<?php
require('configuracion.php');
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);

if ($conn->connect_error) {
 die("Conexión Fallida: ".utf8_decode($conn->connect_error));
} 

?>
