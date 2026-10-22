<?php

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

require('db.php');
$id = $_POST['id'];

$sql = "SELECT * FROM notas WHERE id='".$id."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {   
     while($fila = $result->fetch_assoc()) {         
        $cedula = $fila['codigo'];
        $cod_mat = $fila['cod_mat'];
        $carrera = $fila['carrera'];		    
		    $nota = $fila['nota'];
		    $lapso = $fila['lapso'];
		    $tiplap = $fila['tiplap'];
		    $cod_doc = $fila['cod_doc'];
		    $cod_usu = $fila['cod_usu'];
		    $acu = $fila['acu'];
		    $seccion = $fila['seccion'];
		    $electiva = $fila['electiva'];
		    $fecha = $fila['fecha'];		    
     }
} 

include('/Classes/class_api.php');
$x=new PDF();
$x->notas_auditoria("Borrar_nota",$usuario,$cedula,$cod_mat,$cod_mat_ant,$carrera,$nota,$nota_ant,$lapso,$lapso_ant,$tiplap,$tiplap_ant,$cod_doc,$cod_doc_ant,$cod_usu,$acu,$acu_ant,$seccion,$seccion_ant,$electiva,$electiva_ant);
$x->notas_borrar($id);


?>
