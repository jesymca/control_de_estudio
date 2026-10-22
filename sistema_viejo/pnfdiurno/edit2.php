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
$usuario=$_SESSION['username'];

$accion="Modificar";
$cedula = $_POST['cedula'];
$carrera = $_POST['carrera'];


          require("db.php");

          $sql = "SELECT * FROM alumno WHERE `alumno`.`cedula` = '".$cedula."'";
            $resultado = $conn->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {                 
                    $nombre  = utf8_decode($fila['nombre']);                    
                    $carrera_anterior = $fila['carrera'];               
                    $sexo  = $fila['sexo'];
                    $fechanac  = $fila['fechanac'];
                    $edad  = $fila['edad'];
                    $tipingreso  = $fila['tipingreso'];
                    $ingreso  = $fila['ingreso'];
                    $turno  = $fila['turno'];
                }
            }          

        $carrera_destino =$carrera;   

		$sql = "UPDATE `alumno` SET `carrera` = '".$carrera."' WHERE `alumno`.`cedula` = '".$cedula."'";
        $result = $conn->query($sql);
		
		date_default_timezone_set('America/Caracas');
		$hora = strftime("%I:%M:%S %p\n");
		$fecha = date('d-m-Y');
		
        $usuario=$_SESSION['username'];
     	
		$sql = "INSERT INTO `cambio_carrera` (`cedula`, `nombre`, `sexo`, `fechanac`, `edad`, `tipingreso`, `ingreso`, `turno` , `fecha`, `hora`, `carrera_anterior`, `carrera_destino`,`usuario`) VALUES ('$cedula', '$nombre', '$sexo', '$fechanac', '$edad', '$tipingreso', '$ingreso', '$turno', '$fecha', '$hora','$carrera_anterior','$carrera_destino','$usuario')";
 
        $conn->query($sql);
        $conn->close();
        header('location:cambiar carrera.php');
		
	
?>


