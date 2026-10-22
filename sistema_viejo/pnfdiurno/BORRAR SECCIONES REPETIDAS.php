<?php
include('db.php');  

$sql = "SELECT cod_mat,seccion,lapso,cod_doc, COUNT(*) Total FROM agregarseccion GROUP BY cod_mat,seccion,lapso,cod_doc HAVING COUNT(*) > 1"; 
$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {

	$x=0;

	while($fila = $resultado->fetch_assoc()) { 

		$cod_mat[$x]=$fila["cod_mat"];
		$seccion[$x]=$fila["seccion"];
		$lapso[$x]=$fila["lapso"];
		$cod_doc[$x]=$fila["cod_doc"];
		echo $cod_mat[$x]." - ".$seccion[$x]." - ".$lapso[$x]." - ".$cod_doc[$x]." - Total: ".$fila["Total"]."<br>";
		$total=$resultado->num_rows;

		$x++;
	}

}

echo "<br>";
echo "Cantidad de secciones: ".$x."<br>";
echo "<br>";

for ($i=0; $i <$x+1 ; $i++) { 

	$sql = "SELECT cod_mat FROM notas WHERE cod_mat='".$cod_mat[$i]."' and lapso='".$lapso[$i]."' and seccion='".$seccion[$i]."' and cod_doc='".$cod_doc[$i]."'";
	$resultado = $conn->query($sql); 


	if ($resultado->num_rows > 0) {            
		while($fila = $resultado->fetch_assoc()) { 					   
			$cantidad=$resultado->num_rows;
		}
	}else{
       // echo "DELETE FROM agregarseccion WHERE cod_mat=".$cod_mat[$i]." and lapso=".$lapso[$i]." and seccion=".$seccion[$i]." and cod_doc=".$cod_doc[$i]."<br>";
		$sql = "DELETE FROM `agregarseccion` WHERE cod_mat='".$cod_mat[$i]."' and lapso='".$lapso[$i]."' and seccion='".$seccion[$i]."' and cod_doc='".$cod_doc[$i]."'";
		$resultado = $conn->query($sql);
		echo $cod_mat[$i]."-".$seccion[$i]."-".$lapso[$i]."-".$cod_doc[$i]." ----- La seccion fue borrada"."<br>";
      
	}

	echo $i." ---- ".$cod_mat[$i]."-".$seccion[$i]."-".$lapso[$i]."-".$cod_doc[$i]." ----- Cantida de notas: ".$cantidad."<br>";

	
}

?>