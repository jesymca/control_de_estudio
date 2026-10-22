
<?php

include('conexion.php');

$valor = $_GET['valor'];

$sql2 = new conectarMySQL($servidor, $usuario, $clave, $base_datos);

$sql2->conectar();
$sql2->consultar("SELECT * FROM user WHERE id LIKE '%$valor%' or nombre LIKE '%$valor%' or login LIKE '%$valor%' or clave LIKE '%$valor%'");
$row = $sql2->obtendatos();
echo $row['id'].'|'.$row['nombre'].'|'.$row['login'].'|'.$row['clave'];
sleep(1);
return $dat = $row['id'].'|'.$row['nombre'].'|'.$row['login'].'|'.$row['clave'];
$sql2->cerrarconexion();
$sql2->limpiaconsulta();
?>
