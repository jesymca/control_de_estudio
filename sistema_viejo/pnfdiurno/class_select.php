<?php

class select
{

	public function cargar($query,$value,$text){

		include('db.php');

        //$sql = "SELECT DISTINCT hora FROM horas";
        $sql = $query; 
        $resultado = $conn->query($sql); 

       
		if ($resultado->num_rows > 0) {
               echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				//echo '<option value="'.$fila["hora"].'">'.$fila["hora"].'</option>';
               echo '<option value="'.$value.'">'.$text.'</option>';
			}

		}
	}
	
}