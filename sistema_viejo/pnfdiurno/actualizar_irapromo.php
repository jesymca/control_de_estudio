<?php

function actualizar_irapromo($fe_gr_alu,$marca)
{
	include "db.php";
	$x1=0;
	$num_est=0;
	$sql = "SELECT * FROM `alumno` WHERE marca='".$marca."' and fe_gr_alu='".$fe_gr_alu."' ORDER BY `ira` DESC";
	$resultado = $conn->query($sql);
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$ira[$x1]=$fila['ira'];
			$num_est++;				
			$sql = "UPDATE `alumno` SET ubicacion='".$num_est."' WHERE cedula='".$fila['cedula']."'";
            $conn->query($sql);              
				
		}
	}
   
	
	for ($i=0; $i <count($ira) ; $i++) {         
		$IRA_1=$IRA_1+$ira[$i];
		$IRA_1B=$IRA_1/count($ira)-1;
		$ira=round($IRA_1B, 3);
	} 

    $sql = "UPDATE `alumno` SET irapromo='".$ira."',num_est='".$num_est."' WHERE marca='".$marca."' and fe_gr_alu='".$fe_gr_alu."'";
	$conn->query($sql);

}
