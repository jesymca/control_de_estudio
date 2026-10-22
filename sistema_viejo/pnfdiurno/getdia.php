<?php
        include('db.php');

        $sql = "SELECT DISTINCT descripcion FROM dia"; 
        $resultado = $conn->query($sql); 

       
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["descripcion"].'">'.$fila["descripcion"].'</option>';
               
			}

		}
        





?>