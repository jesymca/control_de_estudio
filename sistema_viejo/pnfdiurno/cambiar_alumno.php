<?php

include('HISTORI.php');
$x=new PDF();

require('configuracion.php');
$base_datos = "termica";
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);

$sql = "SELECT * FROM alumno"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {              
	while($fila = $resultado->fetch_assoc()) { 			
		$esta=$x->buscar_cedula("pnfdiurno",$fila["cedula"],"alumno");
		if($esta=="true"){				  
			$x->borrar_cedula("pnfdiurno",$fila["cedula"],"alumno");
		}	               
	}
}


// if ($resultado->num_rows > 0) {              
//      while($fila = $resultado->fetch_assoc()) { 			
//         $esta=$x->buscar_nota($fila["cedula"]);
//         if($esta=="true"){				  
//               $x->borrar_cedula("termica",$fila["cedula"],"alumno");

//         }	               
//      }
// }

?>