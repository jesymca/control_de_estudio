<?php
        include('db.php');  

               
        $sql = "SELECT DISTINCT docente.cod_doc,docente.nombre FROM docente ORDER BY docente.nombre";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_doc"].'">'.$fila["nombre"].' | '.$fila["cod_doc"].'</option>';

			}
		}        
?>