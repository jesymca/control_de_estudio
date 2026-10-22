<?php
        include('db.php');
        $pensum=$_POST["pensum"];
        $docente=$_POST["cod_doc"];
     

        $sql = "SELECT * FROM seccion";

        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
                                echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["semestre"].' - '.$fila["descrip2"].'</option>';
				
              
			}

		}
        
?>