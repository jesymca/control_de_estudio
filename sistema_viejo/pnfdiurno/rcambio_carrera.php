<?php

// session_start();
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
// } else {
// 	header("Location: index.html");
// 	exit;
// }
// $now = time();
// if($now > $_SESSION['expire']) {
// 	session_destroy();
// 	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
// 	exit;
// }
$id = intval($_GET['id']);
require("db.php");
$sql = "SELECT * FROM alumno WHERE id='" . $id . "'";
$resultado = $conn->query($sql);

while($fila = $resultado->fetch_assoc()) {	
	$cedula=$fila["cedula"];	
}

include('/Classes/class_api.php');
$pdf=new PDF();
$pdf->reporte_cambio_carrera($cedula);

?>
