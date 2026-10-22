<?php
        include('db.php');  

        $sql = "SELECT DISTINCT lismat.pensum,pensum.descripcion2 FROM lismat,pensum WHERE lismat.pensum=pensum.pensum"; 
        $resultado = $conn->query($sql); 
    
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) {

				echo '<option value="'.strtoupper(substr($fila["pensum"], 0, 1)).'">'.$fila["descripcion2"].'</option>';
               
			}
		}
       





?>