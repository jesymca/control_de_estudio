<?php
        include('db.php');  
       
        $sql = "SELECT * FROM `alumno` WHERE actividad='1' ORDER BY `cedula`";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cedula"].'">'.$fila["cedula"].' | '.$fila["nombre"].'</option>';//

			}

		}
        
?>