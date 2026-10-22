<?php
        include('db.php');    

        $sql = "SELECT cod_mat,descrip2,semestre,trayecto FROM lismat ORDER BY pensum";

        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) {
				echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"]." | ".$fila["semestre"]." | ".$fila["descrip2"]." | ".$fila["trayecto"].'</option>';
              
			}

		}
        
?>