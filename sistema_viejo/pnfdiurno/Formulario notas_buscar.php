
<?php

include('conexion.php');

$valor = $_GET['valor'];

$sql2 = new conectarMySQL($servidor, $usuario, $clave, $base_datos);

$sql2->conectar();
$sql2->consultar("SELECT * FROM notas WHERE id LIKE '%$valor%' or codigo LIKE '%$valor%' or cod_mat LIKE '%$valor%' or nota LIKE '%$valor%' or lapso LIKE '%$valor%' or tiplap LIKE '%$valor%' or cod_doc LIKE '%$valor%' or acu LIKE '%$valor%'");
$row = $sql2->obtendatos();
echo $row['id'].'|'.$row['codigo'].'|'.$row['cod_mat'].'|'.$row['nota'].'|'.$row['lapso'].'|'.$row['tiplap'].'|'.$row['cod_doc'].'|'.$row['acu'];
sleep(1);
return $dat = $row['id'].'|'.$row['codigo'].'|'.$row['cod_mat'].'|'.$row['nota'].'|'.$row['lapso'].'|'.$row['tiplap'].'|'.$row['cod_doc'].'|'.$row['acu'];
$sql2->cerrarconexion();
$sql2->limpiaconsulta();
?>
