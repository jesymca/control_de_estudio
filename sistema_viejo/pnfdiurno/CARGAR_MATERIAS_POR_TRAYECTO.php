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
 $grado = $_POST['grado'];
 $lapso=$_POST['lapso'];
 $seccion=$_POST['seccion'];
 $trayecto=$_POST['trayecto'];
 $cod_usu1=$_SESSION['username'];
 $cedula=$_POST['cedula'];


 include('/Classes/class_api.php');
 $x=new PDF();
 $x->cargar_materias_por_trayecto($pensum,$cedula,$cod_mat,$grado,$lapso,$seccion,$trayecto,$cod_usu1);

?>










