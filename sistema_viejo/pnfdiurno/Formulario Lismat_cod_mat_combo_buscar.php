
<?php
require("configuracion.php");
$valor = $_POST['valor_01'];
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
	die("Conexion Fallida: " . $conn->connect_error);
} 
$sql = "SELECT * FROM lismat WHERE cod_mat ='".$valor."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
echo $row['id'].'|'.$row['pensum'].'|'.$row['cod_comp'].'|'.$row['cod_mat'].'|'.$row['descrip2'].'|'.$row['creditos'].'|'.$row['aprobatori'];
	}
}
$conn->close();
?>
