<?php
        include('db.php');  
   
switch ($_POST["carrera"]) {
	
	case 'I':
	$pensum="IXC";
	break;

	default:		
	break;
}


               
        $sql = "SELECT DISTINCT agregarseccion.cod_doc,docente.nombre FROM agregarseccion,docente where pensum='".$pensum."' and docente.cod_doc=agregarseccion.cod_doc ORDER BY docente.nombre";
        $resultado = $conn->query($sql); 


		if ($resultado->num_rows > 0) {
                echo '<option value="">Seleccionar</option>';
			while($fila = $resultado->fetch_assoc()) { 
				echo '<option value="'.$fila["cod_doc"].'">'.$fila["nombre"].' | '.$fila["cod_doc"].'</option>';

			}
		}        
?>