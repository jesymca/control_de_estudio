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

include('/Classes/class_api.php');
$pensum=$_POST["pensum"];
$lapso=$_POST["lapso"];

$pdf=new PDF();

for ($i=0; $i < $pdf->cantidad_materias($pensum,$grado)-1; $i++) { 

if ($pdf->listado_per_una($pensum,$grado,$lapso,$i)>0) {
$pdf->listado_per_todos($pensum,$grado,$cod_mat,$i,$lapso);
}
}

$pdf->Output();


?>
