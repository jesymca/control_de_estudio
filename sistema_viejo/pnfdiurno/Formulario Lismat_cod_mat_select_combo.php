<?php

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


require("configuracion.php");
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
if ($conn->connect_error) {
    die("Conexion Fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM lismat where pensum='".$_SESSION['pensum']."' and nota<>'R'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo '<option value="0" disabled selected>Selecciona una materia</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option value='.$row["cod_mat"].'>'.$row["cod_mat"].' - '.$row["semestre"].' - '.$row["descrip2"].'</option>';
    }
} 
$conn->close();

?>
