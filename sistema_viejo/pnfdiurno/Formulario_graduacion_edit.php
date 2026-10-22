<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
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
        $usuario=$_SESSION['username'];
        $cod=$_SESSION['id'];
        date_default_timezone_set('America/Caracas');
        $hora = strftime("%I:%M:%S %p\n");
        $fecha = date('d-m-Y');
        $accion="Modificar";
        $id=$_POST["id1"];
        $cedula=$_POST["cedula"];
        $nombre=$_POST["nombre"];
        $marca=$_POST["marca"];
        $fe_gr_alu=$_POST["fe_gr_alu"];
        $grado=$_POST["grado"];
        $tipo_estudio=$_POST["tipo_estudio"];



        $sql = "SELECT * FROM alumno WHERE cedula='" . $cedula . "'";
        $resultado = $conn->query($sql);
        
        if (!$resultado) {
            exit;
        }
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {               
                $carrera   = $fila['carrera'];                
            }
        }
        $pensum=$carrera."XC";
        
include('/Classes/class_api.php');
$x=new PDF();


if($grado == "T"){
$cantidad_materias  = $x->cantidad_materias($pensum, $cedula, "T");
$materias_aprobadas = $x->materias_aprobadas($pensum, $cedula, "T");

if ($materias_aprobadas == $cantidad_materias) {

$sql = "INSERT INTO alumno_auditoria(accion,cod,usuario,codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,ingreso,semestre,egreso,pasantia,turno,trabajo,beca,ireceptor,folio,tomo,rusnies,discapacid,pnf,trayecto,hora,fecha) VALUES ('$accion','$cod','$usuario','$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$lugar','$municipio','$estado','$procedenci','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$tipingreso','$ingreso','$semestre','$egreso','$pasantia','$turno','$trabajo','$beca','$ireceptor','$folio','$tomo','$rusnies','$discapacid','$pnf','$trayecto','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

require('db.php');
$sql = "UPDATE alumno SET  marca = '$marca' , fe_gr_alu = '$fe_gr_alu', grado = '$grado', pnf = '$tipo_estudio' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
$x->actualizar_ingreso($cedula,$grado);
$x->actualizar_egreso($cedula,$grado);
$x->actualizar_ira($cedula,$carrera."XC", $grado);

include 'menu.php';
//
$x->mensaje_color("SERVICIOS_g2.php", "principal.php", "Se actualizo el registro", 0, 0);
}
}else {
    header("Location: msg_no_graduado.php");
}

} 

if($grado == "I" OR $grado == "L"){
$cantidad_materias  = $x->cantidad_materias($pensum, $cedula, $grado);
$materias_aprobadas = $x->materias_aprobadas($pensum, $cedula, $grado);



if ($materias_aprobadas == $cantidad_materias) {

$sql = "INSERT INTO alumno_auditoria(accion,cod,usuario,codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,ingreso,semestre,egreso,pasantia,turno,trabajo,beca,ireceptor,folio,tomo,rusnies,discapacid,pnf,trayecto,hora,fecha) VALUES ('$accion','$cod','$usuario','$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$lugar','$municipio','$estado','$procedenci','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$tipingreso','$ingreso','$semestre','$egreso','$pasantia','$turno','$trabajo','$beca','$ireceptor','$folio','$tomo','$rusnies','$discapacid','$pnf','$trayecto','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

require('db.php');
$sql = "UPDATE alumno SET  marca = '$marca' , fe_gr_alu = '$fe_gr_alu', grado = '$grado' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
$x->actualizar_ingreso($cedula,$grado);
$x->actualizar_egreso($cedula,$grado);
$x->actualizar_ira($cedula,$carrera."XC", $grado);

include 'menu.php';

$x->mensaje_color("SERVICIOS_g2.php", "principal.php", "Se actualizo el registro", 0, 0);
}

} else {
    header("Location: msg_no_graduado.php");
}
}


$conn->close();



?>
