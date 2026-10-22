<?php
        include('db.php');

        $sql = "SELECT `tipo` FROM agregarseccion where cod_doc='".$_POST["cod_doc"]."' and cod_mat='".$_POST["cod_mat"]."' and lapso='".$_POST["lapso"]."' and tipo<>'' ORDER BY `tipo`";  
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
			
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["tipo"].'">'.$fila["tipo"].'</option>';

			}

		}
        
?>