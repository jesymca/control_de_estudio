<?php
        include('db.php');

        $sql = "SELECT * FROM aula"; 
        $resultado = $conn->query($sql); 

       
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["aula"]." | ".$fila["descrip"].'">'.$fila["aula"]." | ".$fila["descrip"].'</option>';
               
			}

		}
        





?>