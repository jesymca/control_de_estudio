<?php
        include('db.php');

        $sql = "SELECT DISTINCT hora FROM horas"; 
        $resultado = $conn->query($sql); 

       
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["hora"].'">'.$fila["hora"].'</option>';
               
			}

		}
        





?>