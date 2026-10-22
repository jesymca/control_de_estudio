
<?php
require("configuracion.php");
$valor = $_POST['valor_03'];
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
	die("Conexion Fallida: " . $conn->connect_error);
} 
$sql = "SELECT * FROM docente WHERE cod_doc ='".$valor."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
echo $row['id'].'|'.$row['cod_doc'].'|'.$row['cedula'].'|'.$row['nombre'].'|'.$row['condicion'].'|'.$row['depart'].'|'.$row['sexo'].'|'.$row['fechanac'].'|'.$row['titulo_c'].'|'.$row['titulo_l'].'|'.$row['tipo'].'|'.$row['ingreso'].'|'.$row['categoria'].'|'.$row['dedicacion'].'|'.$row['telefono'].'|'.$row['asignatura'].'|'.$row['horas_ad'].'|'.$row['horas_do'].'|'.$row['observa'].'|'.$row['actividad'].'|'.$row['turno'];
	}
}
$conn->close();
?>
