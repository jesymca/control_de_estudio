<?php

include('db.php');
$pensum=$_POST["pensum"];
$sql = "SELECT DISTINCT descrip2,semestre,grado,trayecto,cod_mat FROM lismat where pensum='".$pensum."' and nota<>'R' ORDER BY trayecto,semestre ASC";
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
  echo '<option value="">Seleccionar</option>';
  while($fila = $resultado->fetch_assoc()) {
    echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["trayecto"].' - '.$fila["semestre"].' - '.$fila["grado"].' - '.$fila["descrip2"].'</option>';
}
}

?>