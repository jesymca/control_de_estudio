<?php
function privilegios($n)
{

  

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
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


$ventana=$n;


switch ($ventana) {

	case 'notas_guardar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and notas_guardar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {

        $notas_guardar=$fila['notas_guardar']; 
        $login=$fila['login'];       	
     	
     }
     $a1=$notas_guardar.$login;
     return $a1;

	break;

	case 'notas_modificar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and notas_modificar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {

     	$notas_modificar=$fila['notas_modificar']; 
     	$login=$fila['login'];  
     }

     $a1=$notas_modificar.$login;
     return $a1;
	break;



	case 'notas_eliminar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and notas_eliminar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {
        
     	$notas_eliminar=$fila['notas_eliminar']; 
        $login=$fila['login'];  	 
     }    
     $a1=$notas_eliminar.$login;
     return $a1;
	break;




    case 'alumno_guardar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and alumno_guardar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {

        $notas_guardar=$fila['alumno_guardar']; 
        $login=$fila['login'];          
        
     }
     $a1=$notas_guardar.$login;
     return $a1;

    break;

    case 'alumno_modificar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and alumno_modificar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {

        $notas_modificar=$fila['alumno_modificar']; 
        $login=$fila['login'];  
     }

     $a1=$notas_modificar.$login;
     return $a1;
    break;



    case 'alumno_eliminar':
     include('configuracion.php');
     $enlace = mysql_connect($servidor, $usuario, $clave);
     mysql_select_db($base_datos, $enlace);

     $sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."' and alumno_eliminar='1'";
     $resultado = mysql_query($sql, $enlace);

     while ($fila = mysql_fetch_assoc($resultado)) {
        
        $notas_eliminar=$fila['alumno_eliminar']; 
        $login=$fila['login'];       
     }    
     $a1=$notas_eliminar.$login;
     return $a1;
    break;

}





}