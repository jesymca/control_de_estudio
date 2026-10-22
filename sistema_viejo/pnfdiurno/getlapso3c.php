<?php
        include('db.php');          
        $sql = "SELECT DISTINCT `lapso` FROM agregarseccion where cod_mat='".$_POST["cod_mat"]."' and cod_doc='".$_POST["cod_doc"]."'"; 
        $resultado = $conn->query($sql);         
		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["lapso"].'">'.$fila["lapso"].'</option>';
        	}
		}
?>