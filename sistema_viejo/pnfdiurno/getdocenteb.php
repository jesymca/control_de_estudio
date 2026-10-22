<?php
        include('db.php');  
        $sql = "SELECT * FROM docente,agregarseccion where pensum='".$_POST["pensum"]."' and docente.cod_doc=agregarseccion.cod_doc";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_doc"].'">'.$fila["nombre"].' | '.$fila["cod_doc"].'</option>';

			}

		}
        
?>