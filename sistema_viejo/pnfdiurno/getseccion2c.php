<?php
        include('db.php');  

        $sql = "SELECT DISTINCT agregarseccion.seccion,agregarseccion.lapso,agregarseccion.cod_mat,lismat.descrip2 FROM agregarseccion,lismat where cod_doc='".$_POST["cod_doc"]."' and agregarseccion.cod_mat=lismat.cod_mat"; 
        $resultado = $conn->query($sql); 

       
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' | '.$fila["descrip2"].' | '.$fila["lapso"].' | '.$fila["seccion"].'</option>';
               
			}

		}
        





?>