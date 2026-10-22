<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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

$usuario1=$_SESSION['username'];
$cod1=$_SESSION['id'];

if($_POST["editval"]=="0" AND $_POST["editval"]<>"00" OR $_POST["editval"]=="01" OR $_POST["editval"]=="02"  OR $_POST["editval"]=="03"  OR $_POST["editval"]=="04"  OR $_POST["editval"]=="05"  OR $_POST["editval"]=="06"  OR $_POST["editval"]=="07"  OR $_POST["editval"]=="08"  OR $_POST["editval"]=="09"  OR $_POST["editval"]=="10"  OR $_POST["editval"]=="11"  OR $_POST["editval"]=="12"  OR $_POST["editval"]=="13"  OR $_POST["editval"]=="14"  OR $_POST["editval"]=="15"  OR $_POST["editval"]=="16"  OR $_POST["editval"]=="17"  OR $_POST["editval"]=="18"  OR $_POST["editval"]=="19"  OR $_POST["editval"]=="20" OR $_POST["editval"]=="IN"){
	

$nota=$_POST["editval"];


$id = $_POST['id'];

require('db.php');

$sql = "SELECT * FROM notas WHERE id='".$id."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	
   
     while($row = $result->fetch_assoc()) {        
        
            $codigo = $row['codigo'];
		    $seccion = $row['seccion'];
		    $cod_mat = $row['cod_mat'];
		    $nota2 = $row['nota'];
		    $lapso = $row['lapso'];
		    $tiplap = $row['tiplap'];
		    $cod_doc = $row['cod_doc'];		   
		    $acu = $row['acu'];

     }
} 
$conn->close();

include('/Classes/class_api.php');
$x=new PDF();

$x->notas_auditoria("Modificar_nota_sec",$usuario,$codigo,$cod_mat,$cod_mat_ant,$carrera,$nota,$nota2,$lapso,$lapso_ant,$tiplap,$tiplap_ant,$cod_doc,$cod_doc_ant,$cod_usu,$acu,$acu_ant,$seccion,$seccion_ant,$electiva,$electiva_ant);


if($nota>0){
	$total=$nota*5;	
}else{
	$total=0;
}
require('db.php');
$sql = "UPDATE notas set " . $_POST["column"] . " = '".$nota."', acu = '".$total."' WHERE  id=".$_POST["id"];

if ($conn->query($sql) === TRUE) {
    echo "Se Modifico la Nota";
} else {
   echo "No se pudo Modificar la nota";
} 



$conn->close();

}

?>
