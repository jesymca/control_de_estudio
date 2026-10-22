<?php

include('db.php');
$pensum=$_POST["pensum"];

$sql = "SELECT descrip2,cod_mat,semestre,grado,trayecto FROM lismat where pensum='".$pensum."' and `cod_mat` LIKE '%R%' AND SUBSTRING(`cod_mat`,2,1)<>'P' AND SUBSTRING(`cod_mat`,2,1)<>'E' AND SUBSTRING(`cod_mat`,2,1)<>'T' AND SUBSTRING(`cod_mat`,2,1)<>'0' AND SUBSTRING(`cod_mat`,2,1)<>'1' AND SUBSTRING(`cod_mat`,2,1)<>'2' AND SUBSTRING(`cod_mat`,2,1)<>'3' AND aprobatori<>16 ORDER BY trayecto,semestre ASC";

$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
  echo '<option value="">Seleccionar</option>';
  while($fila = $resultado->fetch_assoc()) {
    echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["trayecto"].' - '.$fila["semestre"].' - '.$fila["grado"].' - '.$fila["descrip2"].'</option>';
}
}

?>