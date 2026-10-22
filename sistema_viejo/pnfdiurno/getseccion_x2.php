<?php
        include('db.php');
        $pensum=$_POST["pensum"];
       
     

        $sql = "SELECT DISTINCT cod_mat,descrip2,cod_mat,semestre FROM lismat where pensum='".$pensum."'";

        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 

                                if(substr($fila["cod_mat"], 1, 1)<>"R"){
                                echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["semestre"].' - '.$fila["descrip2"].'</option>';
				}
              
			}

		}
        
?>