<?php
        include('db.php');  

        $sql = "SELECT * FROM electivas"; 
        $resultado = $conn->query($sql); 
        $primero=$resultado->num_rows;
       

        $sql = "SELECT * FROM cod_electivas"; 
        $resultado = $conn->query($sql);
        $ultimo=$resultado->num_rows;
        


        
        $sql = "SELECT * FROM cod_electivas LIMIT $primero,$ultimo"; 
        $resultado = $conn->query($sql); 
    
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_ele"].'">'.$fila["cod_ele"].'</option>';
               
			}
		}


?>