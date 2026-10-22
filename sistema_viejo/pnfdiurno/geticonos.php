<?php
        include('db.php');  

        $sql = "SELECT DISTINCT * FROM iconos"; 
        $resultado = $conn->query($sql); 
    
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["icono"].'">'.$fila["icono"].'</option>';
               
			}
		}
?>