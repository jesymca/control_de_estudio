<?php
include('db.php'); 
  
$pensum=$_POST["pensum"];
$trayecto=$_POST["trayecto"];

$sql = "SELECT * FROM lismat where pensum='".$pensum."' and nota<>'R' and SUBSTRING(`cod_mat`,2,1)<>'P' and SUBSTRING(`cod_mat`,2,1)<>'T' and SUBSTRING(`cod_mat`,2,1)<>'E' and trayecto='".$trayecto."' ORDER BY `id` ASC";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
                while($fila = $resultado->fetch_assoc()) { 
                    
                 echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["trayecto"].' - '.$fila["semestre"].' - '.$fila["grado"].' - '.$fila["descrip2"].'</option>';				
                      
                      
                }
      }
      ?>