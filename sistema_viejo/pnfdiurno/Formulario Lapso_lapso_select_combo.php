
<?php
require("configuracion.php");
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
    die("Conexion Fallida: " . $conn->connect_error);
}
$sql = "SELECT * FROM lapso ORDER BY lapso DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo '<option value="0" disabled selected>Selecciona un lapso</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option>'.$row["lapso"].'</option>';
    }
} 
$conn->close();
?>
