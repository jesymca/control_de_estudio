<?php
        include('db.php');       
        $cedula = $_POST["cedula"]; 
        $sql = "SELECT cedula FROM `alumno` WHERE cedula='".$cedula."'";
        $resultado = $conn->query($sql); 

		if ($resultado->num_rows > 0) {                
			echo "Ya existe";
		}else{
            echo "No existe";
		}


        
?>