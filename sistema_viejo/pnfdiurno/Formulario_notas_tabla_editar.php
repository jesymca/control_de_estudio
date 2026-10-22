<?php

 require('db.php');

 $id = $_POST['id'];
 $codigo = $_POST['codigo'];
 $cod_mat = $_POST['cod_mat'];
 $seccion = $_POST['seccion'];
 $nota = $_POST['nota'];
 $lapso = $_POST['lapso'];
 $tiplap = $_POST['tiplap'];
 $cod_doc = $_POST['cod_doc'];
 $acu = $_POST['acu'];

  //if(isset($_POST['action']) == 'editar'){
	
    $query = "UPDATE notas SET cod_mat = '".$cod_mat.$seccion."',nota = '".$nota."',acu = '".$acu."',lapso = '".$lapso."',tiplap = '".$tiplap."',cod_doc = '".$cod_doc."' WHERE id = '".$id."'";
    mysqli_query($conn, $query);

	if ($conn->query($sql) === TRUE) {
		echo "Nota actualizada";
		require ("aud.php");
        auditar("notas_B",$codigo);		
		header("Location: Formulario_notas_tabla_index.php");
	} else {
		echo "Error al actualizar la nota: ".$conn->error;
		header("Location: Formulario_notas_tabla_index.php");
	}

	$conn->close();

   
    
     

    header("Location: index.php");
 //}



?>
