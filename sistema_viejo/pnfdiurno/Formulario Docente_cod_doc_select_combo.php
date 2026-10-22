
<?php
require("configuracion.php");
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
    die("Conexion Fallida: " . $conn->connect_error);
}
$sql = "SELECT * FROM docente ORDER BY nombre ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo '<option value="0" disabled selected>Selecciona un docente</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option value='.$row["cod_doc"].'>'.$row["nombre"]." | ".$row["cod_doc"].'</option>';
    }
} 
$conn->close();
?>
