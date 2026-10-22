<?php
        include('db.php');  

        $sql = "SELECT DISTINCT cod_doc FROM agregarseccion where cod_doc='".$_POST["cod_doc"]."' and seccion='".$_POST["seccion"]."' and lapso='".$_POST["lapso"]."' and tiplap='".$_POST["tiplap"]."'"; 
        $resultado = $conn->query($sql); 

        $provincias = array();
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].' | '.$fila["lapso"].'</option>';
               
			}

		}
        





?>