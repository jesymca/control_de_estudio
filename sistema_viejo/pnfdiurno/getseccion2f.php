<?php
        include('db.php');

        $sql = "SELECT DISTINCT seccion FROM agregarseccion where cod_doc='".$_POST["cod_doc"]."' and cod_mat='".$_POST["cod_mat"]."' and lapso='".$_POST["lapso"]."'"; 
        
        
        $resultado = $conn->query($sql); 

        $provincias = array();
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';
               
			}

		}
        





?>