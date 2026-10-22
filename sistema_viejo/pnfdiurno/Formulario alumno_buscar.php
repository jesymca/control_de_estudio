
<?php
include('conexion.php');
$valor = $_GET['valor'];
$sql2 = new conectarMySQL($servidor, $usuario, $clave, $base_datos);
$sql2->conectar();
$sql2->consultar("SELECT * FROM alumno WHERE cedula LIKE '%$valor%' or nombre LIKE '%$valor%'");
$row = $sql2->obtendatos();
echo $row['id'].'|'.$row['codigo'].'|'.$row['cedula'].'|'.$row['nombre'].'|'.$row['carrera'].'|'.$row['mencion'].'|'.$row['plan'].'|'.$row['actividad'].'|'.$row['sexo'].'|'.$row['edocivil'].'|'.$row['lugar'].'|'.$row['municipio'].'|'.$row['estado'].'|'.$row['procedenci'].'|'.$row['fechanac'].'|'.$row['edad'].'|'.$row['direccion'].'|'.$row['telefonoh'].'|'.$row['telefonoc'].'|'.$row['telefonot'].'|'.$row['email'].'|'.$row['tipingreso'].'|'.$row['ingreso'].'|'.$row['egreso'].'|'.$row['pasantia'].'|'.utf8_decode($row['turno']).'|'.$row['trabajo'].'|'.$row['beca'].'|'.$row['ireceptor'].'|'.$row['folio'].'|'.$row['tomo'].'|'.$row['rusnies'].'|'.$row['discapacid'].'|'.$row['pnf'].'|'.$row['fcedula'].'|'.$row['inscripmilt'].'|'.$row['ftitulo'].'|'.$row['fcerfidicado'].'|'.$row['fnotas'].'|'.$row['fdosfotos'].'|'.$row['fpinscrip'].'|'.$row['fdepbanc'].'|'.$row['fnacimie'];
sleep(1);
return $dat = $row['id'].'|'.$row['codigo'].'|'.$row['cedula'].'|'.$row['nombre'].'|'.$row['carrera'].'|'.$row['mencion'].'|'.$row['plan'].'|'.$row['actividad'].'|'.$row['sexo'].'|'.$row['edocivil'].'|'.$row['lugar'].'|'.$row['municipio'].'|'.$row['estado'].'|'.$row['procedenci'].'|'.$row['fechanac'].'|'.$row['edad'].'|'.$row['direccion'].'|'.$row['telefonoh'].'|'.$row['telefonoc'].'|'.$row['telefonot'].'|'.$row['email'].'|'.$row['tipingreso'].'|'.$row['ingreso'].'|'.$row['egreso'].'|'.$row['pasantia'].'|'.utf8_decode($row['turno']).'|'.$row['trabajo'].'|'.$row['beca'].'|'.$row['ireceptor'].'|'.$row['folio'].'|'.$row['tomo'].'|'.$row['rusnies'].'|'.$row['discapacid'].'|'.$row['pnf'].'|'.$row['fcedula'].'|'.$row['inscripmilt'].'|'.$row['ftitulo'].'|'.$row['fcerfidicado'].'|'.$row['fnotas'].'|'.$row['fdosfotos'].'|'.$row['fpinscrip'].'|'.$row['fdepbanc'].'|'.$row['fnacimie'];
$sql2->cerrarconexion();
$sql2->limpiaconsulta();
?>
