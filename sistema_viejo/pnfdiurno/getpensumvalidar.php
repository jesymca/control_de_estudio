<?php
        include('db.php');

        
        $sql = "SELECT DISTINCT pensum FROM lismat where pensum='".$_POST["pensum"]."'"; 
        $resultado = $conn->query($sql); 

        
		if ($resultado->num_rows > 0) {
               
			//while($fila = $resultado->fetch_assoc()) { 
				//echo $fila["pensum"];
               
			//}


		}else{
            echo "No es valido";
		}
        





?>