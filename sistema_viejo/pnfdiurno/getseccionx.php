<?php
        include('db.php');

        $docente=$_POST["cod_doc"];


        $sql = "SELECT DISTINCT seccion FROM seccion";

        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["seccion"].'">'.$fila["seccion"].'</option>';
              
			}

		}
        
?>