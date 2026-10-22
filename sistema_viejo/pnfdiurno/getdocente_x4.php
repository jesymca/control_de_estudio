<?php
        include('db.php'); 
               
        $sql = "SELECT nombre,cedula,cod_doc FROM docente ORDER BY nombre";
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_doc"].'">'.$fila["nombre"].' | '.$fila["cedula"].' | '.$fila["cod_doc"].'</option>';
			}
		}  

?>