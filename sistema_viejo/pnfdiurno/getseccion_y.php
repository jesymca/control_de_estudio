<?php

include('db.php');
$pensum=$_POST["pensum"];
$docente=$_POST["cod_doc"];

$sql = "SELECT DISTINCT lismat.descrip2,lismat.cod_mat,lismat.semestre,lismat.trayecto,agregarseccion.cod_mat FROM agregarseccion,lismat where agregarseccion.pensum='".$pensum."' and agregarseccion.cod_doc='".$docente."' and agregarseccion.cod_mat=lismat.cod_mat ORDER BY lismat.trayecto ASC";
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
        echo '<option value="">Seleccionar</option>';
        while($fila = $resultado->fetch_assoc()) {
        echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["trayecto"].' - '.$fila["semestre"].' - '.$fila["descrip2"].'</option>';
        }
}

?>