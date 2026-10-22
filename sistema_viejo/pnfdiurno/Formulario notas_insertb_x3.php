<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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


$pensum=$_POST['pensum'];
$cod_mat=$_POST['cod_mat'];
$electiva=$_POST['electiva'];
$lapso=$_POST['lapso'];
$seccion=$_POST['seccion'];
$cedula=$_POST['cedula'];
$cod_doc=$_POST['cod_doc'];
$usuario=$_SESSION['username'];
$carrera=substr($cod_mat, 0, 1);
$pensum=substr($cod_mat, 0, 1)."XC";

include('/Classes/class_api.php');
$x=new PDF();
                            
$x->guarda_seccion_2_mensaje3($pensum,$cedula,$cod_mat,$cod_doc,$lapso,$seccion,$usuario,1);
$x->notas_auditoria("Inscribir_una_varios",$usuario,$cedula,$cod_mat,$cod_mat_ant,$carrera,$nota,$nota_ant,$lapso,$lapso_ant,$tiplap,$tiplap_ant,$cod_doc,$cod_doc_ant,$cod_usu,$acu,$acu_ant,$seccion,$seccion_ant,$electiva,$electiva_ant);

?>
