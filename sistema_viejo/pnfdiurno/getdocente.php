<?php
        include('db.php');  
        $sql = "SELECT DISTINCT cod_doc,nombre FROM docente ORDER BY nombre";
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_doc"].'">'.$fila["nombre"].' | '.$fila["cod_doc"].'</option>';
			}
		}
        
?>