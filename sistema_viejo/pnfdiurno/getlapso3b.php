<?php
        include('db.php');  

        $sql = "SELECT * FROM agregarseccion where cod_doc='".$_POST["cod_doc"]."'"; 
        $resultado = $conn->query($sql); 

        
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';
               
			}

		}
        





?>