<?php
        include('db.php');       

        $sql = "SELECT DISTINCT cedula,nombre FROM `alumno` WHERE actividad='1' ORDER BY `nombre`";
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cedula"].'">'.$fila["nombre"].' | '.$fila["cedula"].'</option>';//
			}
		}
        
?>