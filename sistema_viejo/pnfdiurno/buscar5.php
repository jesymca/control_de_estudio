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

$accion="Guardar_copiar";
$cod=$_SESSION['id'];
include('/Classes/class_api.php');
$x=new PDF();

require("db.php");

date_default_timezone_set('America/Caracas');
$fecha = date('d-m-Y');
$hora = strftime("%I:%M:%S %p\n");

$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$cod_doc = $_POST['cod_doc'];
$seccion = $_POST['seccion'];
$cantidad=$_POST["cantidad"];
$carrera=$_POST["pensum"];

$sql = "SELECT * FROM notas WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {


  $cod_mat2 = $_POST['cod_mat2'];
  $lapso2 = $_POST['lapso2'];
  $cod_doc2 = $_POST['cod_doc2'];
  $seccion2 = $_POST['seccion2']; 


  if($_POST['Radios']=="A"){
   $connotas=0;
  }else{      
   $connotas=1;
  }


 $sql = "SELECT * FROM notas WHERE cod_mat='".$cod_mat2."' AND `lapso`='".$lapso2."' AND `cod_doc`='".$cod_doc2."' AND `seccion`='".$seccion2."'"; 
 $result = $conn->query($sql);

 if ($result->num_rows > 0) {

  $materialapso="0";
  include 'menu.php';
           
  $x->mensaje_color("copiar_seccion_1.php", "principal.php", "Seccion ya cargada", 0, 0);
 

}else{

  $sql = "SELECT * FROM notas WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'";
  $result = $conn->query($sql);
  $x1=0;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cedula[$x1]=$row["codigo"];
      
      if ($connotas==1){
        
        // $carrera[$x1]=$row["carrera"];
        $nota[$x1]=$row["nota"]; 
        $acu[$x1]=$row["acu"];               
        $electiva[$x1]=$row["electiva"];
        $cod_usu=$_SESSION['username'];

      }else{
       
        // $carrera[$x1]=$row["carrera"];
        $nota[$x1]=0;
        $acu[$x1]=0;              
        $electiva[$x1]=$row["electiva"];
        $cod_usu=$_SESSION['username'];
     }
     $x1++;    
   }
 }
 
 $usuario=$_SESSION['username'];
 for ($i=0; $i <=$cantidad-1 ; $i++) {

  $sql = "INSERT INTO notas_auditoria (`accion`, `usuario`, `cedula`, `cod_mat`,`cod_mat_ant`,`nota`, `lapso`, `lapso_ant`, `cod_doc`, `cod_doc_ant`, `seccion`, `seccion_ant`,`hora`, `fecha`) VALUES ('$accion', '$usuario', '$cedula[$i]', '$cod_mat', '$cod_mat2','$nota[$i]', '$lapso', '$lapso2', '$cod_doc', '$cod_doc2', '$seccion', '$seccion2','$hora', '$fecha')";
  $result = $conn->query($sql);       

  $sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,carrera,seccion,fecha)
  VALUES ('$cedula[$i]','$cod_mat2','$nota[$i]','$lapso2','$tiplap','$cod_doc2','$usuario','$acu[$i]','$carrera','$seccion2','$fecha')";
  $result = $conn->query($sql);

 } 

$conn->close(); 

include 'menu.php';
$x->mensaje_color("copiar_seccion_1.php", "principal.php", "Copia Exitosa", 0, 0);

}

}else{

$x->mensaje_color("copiar_seccion_1.php", "principal.php", "Copia Exitosa", 0, 0);

} 

?>


