<?php
require('configuracion.php');

if (!mysql_connect($servidor, $usuario, $clave)) {
	echo 'Could not connect to mysql';
	exit;
}

$sql = "SHOW TABLES FROM ".$base_datos."";
$result = mysql_query($sql);

if (!$result) {
	echo "DB Error, could not list tables\n";
	echo 'MySQL Error: ' . mysql_error();
	exit;
}

while ($row = mysql_fetch_row($result)) {
	echo $row[0]."<br>";       
}

mysql_free_result($result);






mysql_connect(servidor, usuario, clave);
mysql_select_db(base_datos);

$resultado = mysql_query("SHOW COLUMNS FROM ".$tabla);
if (!$resultado) {
	echo "No se pudo ejecutar la consulta:". mysql_error();
	exit;
}

if (mysql_num_rows($resultado) > 0) {
	while ($fila = mysql_fetch_assoc($resultado)) {
		$nombre=$fila["Field"];

	}
}




?>
