<?php
require('db.php');
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_borrar']==1) {
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

if(!empty($_POST['id'])) {
	$id = $_POST['id'];

	$sql = "SELECT * FROM notas WHERE id='".$id."'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {


		while($row = $result->fetch_assoc()) {        

			$cedula = $row['codigo'];
			$seccion = $row['seccion'];
			$cod_mat = $row['cod_mat'].$row['seccion'];
			$nota = $row['nota'];
			$lapso = $row['lapso'];
			$tiplap = $row['tiplap'];
			$cod_doc = $row['cod_doc'];		   
			$acu = $row['acu'];
			$cod_usu = $row['cod_usu'];
		}
	} 

	$usuario=$_SESSION['username'];

    include('/Classes/class_api.php');
    $x=new PDF();
    $x->notas_auditoria("Borrar_nota_sec",$usuario,$cedula,$cod_mat,$cod_mat_ant,$carrera,$nota,$nota_ant,$lapso,$lapso_ant,$tiplap,$tiplap_ant,$cod_doc,$cod_doc_ant,$cod_usu,$acu,$acu_ant,$seccion,$seccion_ant,$electiva,$electiva_ant);
    $x->notas_borrar($id);
	
}
?>