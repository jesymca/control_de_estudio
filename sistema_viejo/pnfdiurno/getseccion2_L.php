<?php
        include('db.php');

        $sql = "SELECT DISTINCT seccion FROM agregarseccion where lapso='".$_POST["lapso"]."' ORDER BY seccion DESC"; 
       
        $resultado = $conn->query($sql); 

        $provincias = array();
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';
             
			}

		}
        





?>