<?php

require("db.php");

$cedula = $_POST['cedula'];
$codigo = $cedula
$nombre = $_POST['nombre'];
$grado = $_POST['grado'];
$carrera = $_POST['carrera'];
$mencion = $_POST['mencion'];
$plan = $_POST['plan'];
$actividad = $_POST['actividad'];
$sexo = $_POST['sexo'];
$edocivil = $_POST['edocivil'];
$lugar = $_POST['lugar'];
$municipio = $_POST['municipio'];
$estado = $_POST['estado'];
$procedenci = $_POST['procedenci'];
$fechanac = $_POST['fechanac'];
$edad = $_POST['edad'];
$direccion = $_POST['direccion'];
$telefonoh = $_POST['telefonoh'];
$telefonoc = $_POST['telefonoc'];
$telefonot = $_POST['telefonot'];
$email = $_POST['email'];
$tipingreso = $_POST['tipingreso'];
$ingreso = $_POST['ingreso'];
$semestre = $_POST['semestre'];
$egreso = $_POST['egreso'];
$pasantia = $_POST['pasantia'];
$turno = $_POST['turno'];
$trabajo = $_POST['trabajo'];
$beca = $_POST['beca'];
$rusnies = $_POST['rusnies'];
$ireceptor = $_POST['ireceptor'];
$discapacid = $_POST['discapacid'];
$tomo = $_POST['tomo'];
$folio = $_POST['folio'];
$pnf = $_POST['pnf'];
$trayecto = $_POST['trayecto'];
$fcedula = $_POST['fcedula'];
$inscripmilt = $_POST['inscripmilt'];
$ftitulo = $_POST['ftitulo'];
$fcerfidicado = $_POST['fcerfidicado'];
$fnotas = $_POST['fnotas'];
$fdosfotos = $_POST['fdosfotos'];
$fpinscrip = $_POST['fpinscrip'];
$fdepbanc = $_POST['fdepbanc'];
$fnacimie = $_POST['fnacimie'];
$id = $_POST['id'];

$sql = "UPDATE alumno SET  codigo = '$codigo' , cedula = '$cedula' , nombre = '$nombre' , grado = '$grado', carrera = '$carrera' , mencion = '$mencion' , plan = '$plan' , actividad = '$actividad' , sexo = '$sexo' , edocivil = '$edocivil' , lugar = '$lugar' , municipio = '$municipio' , estado = '$estado' , procedenci = '$procedenci' , fechanac = '$fechanac' , edad = '$edad' , direccion = '$direccion' , telefonoh = '$telefonoh' , telefonoc = '$telefonoc' , telefonot = '$telefonot' , email = '$email' , tipingreso = '$tipingreso' , ingreso = '$ingreso' , semestre = '$semestre' , egreso = '$egreso' , pasantia = '$pasantia' , turno = '$turno' , trabajo = '$trabajo' , beca = '$beca' , rusnies = '$rusnies' , ireceptor = '$ireceptor' , discapacid = '$discapacid' , tomo = '$tomo' , folio = '$folio' , pnf = '$pnf' , trayecto = '$trayecto' , fcedula = '$fcedula' , inscripmilt = '$inscripmilt' , ftitulo = '$ftitulo' , fcerfidicado = '$fcerfidicado' , fnotas = '$fnotas' , fdosfotos = '$fdosfotos' , fpinscrip = '$fpinscrip' , fdepbanc = '$fdepbanc' , fnacimie = '$fnacimie' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
    echo "registro actualizado";
} else {
    echo "Error al actualizar el registro: ".$conn->error;
}

$conn->close();



?>
