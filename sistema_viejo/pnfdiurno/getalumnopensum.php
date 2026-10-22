<?php
        include('db.php');

         
        $carrera=substr($_POST["pensum"], 0, 1);
        $plan=substr($_POST["pensum"], 2, 1);

        $sql = "SELECT DISTINCT cedula,nombre FROM `alumno` WHERE carrera='".$carrera."' and plan='".$plan."' and actividad='1' ORDER BY `nombre`";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cedula"].'">'.$fila["nombre"].' | '.$fila["cedula"].'</option>';//

			}

		}
        
?>