<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
} else {
	header("Location: index.html");
	exit;
}
$now = time();
if($now > $_SESSION['expire']) {
	session_destroy();
	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
	exit;
}

// require ("aud.php");
// auditar("Horario de clase",$_POST["cod_mat"]." - ".$_POST["LAPSO"]);

	include "db.php";
   
   	$query = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_array($result))
	{                      
		$usuario1 = $row['nombre'];
	}   

    $lapso=$_POST["lapso"];
    $pensum=$_POST["pensum"];
    $seccion=$_POST["seccion"];	
    $cedula=$_POST["cedula"];


    $X1B=0;
    include('/Classes/class_api.php');
    $pdf=new PDF('L','mm','A4');
    $pdf->Encabezado_horario_de_clase($pensum,$cedula,$lapso,$seccion,$usuario1);

?>
