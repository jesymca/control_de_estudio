<?php
        include('db.php');     
			
   
        $sql = "SELECT DISTINCT `tiplap` FROM notas where tiplap<>'' and cod_doc='".$_POST["cod_doc"]."' and lapso='".$_POST["lapso"]."' and cod_mat='".$_POST["cod_mat"]."'"; 
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {
			
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["tiplap"].'">'.$fila["tiplap"].'</option>';

			}

		}

		
        
?>