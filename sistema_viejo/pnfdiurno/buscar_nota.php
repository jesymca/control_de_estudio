<?php



function buscar_nota($CODIGO,$COD_MAT)
{

	include('db.php'); 

	$sql = "SELECT * FROM notas WHERE CODIGO = '".$CODIGO."' AND COD_MAT LIKE '%".$COD_MAT."%'";
	$resultado = $conn->query($sql);
	

	if (!$resultado) {	
		exit;
	}

	while ($fila = mysql_fetch_assoc($resultado)) {  
		return $fila['NOTA'];			

	}		

	
}


function buscar_LAPSO($CODIGO,$COD_MAT)
{
	include('db.php');

	$sql = "SELECT * FROM notas WHERE CODIGO = '".$CODIGO."' AND COD_MAT LIKE '%".$COD_MAT."%'";

	$resultado = $conn->query($sql);
	if (!$resultado) {	
		exit;
	}
	while ($fila = mysql_fetch_assoc($resultado)) {  
		return $fila['LAPSO'];
	}	
	
}


function buscar_TIPLAP($CODIGO,$COD_MAT)
{

	include('db.php');

	$sql = "SELECT * FROM notas WHERE CODIGO = '".$CODIGO."' AND COD_MAT LIKE '%".$COD_MAT."%'";

	$resultado = $conn->query($sql);

	if (!$resultado) {	
		exit;
	}

	while ($fila = mysql_fetch_assoc($resultado)) {  
		return $fila['TIPLAP'];
	}		

	
}

?>