
<?php
require("configuracion.php");
$valor = $_POST['valor_06'];
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
	die("Conexion Fallida: " . $conn->connect_error);
} 
$sql = "SELECT * FROM seccion WHERE seccion ='".$valor."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
echo $row['id'].'|'.$row['seccion'];
	}
}
$conn->close();
?>
