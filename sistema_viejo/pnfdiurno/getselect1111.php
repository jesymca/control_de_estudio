<?php

$pensum="IXC";
$grado="T";


include('db.php');
$sql="SELECT * FROM lismat where  pensum='".$pensum."' and grado='".$grado."' and nota<>'R' ORDER BY `id` ASC";
$resultado=$conn->query($sql);

if ($resultado->num_rows > 0) {
	        echo '<option value="">Seleccionar</option>';
	while ($fila=$resultado->fetch_assoc()) {
		if (substr($fila["cod_mat"], 1, 1) == "P") {
			echo '<option value="' . $fila["cod_mat"] . '" style="color:#FF2F00;background:#FBFCBE;">' . $fila["cod_mat"] . ' - ' . $fila["trayecto"] . ' - ' . $fila["semestre"] . ' - ' . $fila["grado"] . ' - ' . $fila["descrip2"] . '</option>';
		} elseif (substr($fila["cod_mat"], 1, 1) == "T") {
			echo '<option value="' . $fila["cod_mat"] . '" style="color:#002FFF;background:#FBFCBE;">' . $fila["cod_mat"] . ' - ' . $fila["trayecto"] . ' - ' . $fila["semestre"] . ' - ' . $fila["grado"] . ' - ' . $fila["descrip2"] . '</option>';
		} elseif (substr($fila["cod_mat"], 1, 1) == "E") {
			echo '<option value="' . $fila["cod_mat"] . '" style="color:#0F5500;background:#FBFCBE;">' . $fila["cod_mat"] . ' - ' . $fila["trayecto"] . ' - ' . $fila["semestre"] . ' - ' . $fila["grado"] . ' - ' . $fila["descrip2"] . '</option>';
		} else {
			echo '<option value="' . $fila["cod_mat"] . '" style="color:#000000;background:#C2F8E7;">' . $fila["cod_mat"] . ' - ' . $fila["trayecto"] . ' - ' . $fila["semestre"] . ' - ' . $fila["grado"] . ' - ' . $fila["descrip2"] . '</option>';
		}
	}
}



?>