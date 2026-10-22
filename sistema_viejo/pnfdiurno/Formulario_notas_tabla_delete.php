<?php
	
require('db.php');
if(isset($_GET['action']) == 'delete'){
	$id = intval($_GET['id']);
	
	$sql = "DELETE FROM notas WHERE id ='".$id."'";

	if ($conn->query($sql) === TRUE) {
		echo "Nota Borrada";
		require ("aud.php");
        auditar("notas_D",$codigo);
		header("Location: Formulario_notas_tabla_index.php");
	} else {
		echo "Error al Borrar la nota: ".$conn->error;
		header("Location: Formulario_notas_tabla_index.php");
	}

	$conn->close();

}

?>
