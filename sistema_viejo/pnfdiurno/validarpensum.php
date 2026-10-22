<?php
        include('db.php');

         
        $pensum=$_POST["pensum"];
      

        $sql = "SELECT DISTINCT pensum FROM lismat WHERE pensum='".$pensum."'";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
		}else{
                        echo "el pensum existe";
                }
        
?>