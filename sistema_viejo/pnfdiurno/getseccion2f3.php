<?php
        include('db.php');

        $sql = "SELECT * FROM `seccion` ORDER BY `id`"; 
        
        
        $resultado = $conn->query($sql); 

        $provincias = array();
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';
               
			}

		}
        





?>