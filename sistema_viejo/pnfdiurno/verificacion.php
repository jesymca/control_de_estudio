<?php



require ("Security.php");
session_start();
require("db.php");

$username = strtoupper($_POST['usuario']);
$password = encriptar($_POST['clave']);


$sql = "select * from user WHERE login = '$username' and clave = '$password'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {

	$_SESSION['loggedin'] = true;
	$_SESSION['username'] = $username;
	$_SESSION['start'] = time();
	$_SESSION['expire'] = $_SESSION['start'] + (180 * 60);
	$_SESSION['id'] = 0;
	$_SESSION['pensum'] = 0;
	$_SESSION['alumno_sec'] = "";
	$_SESSION['trayecto_sec'] = ""; 
	$_SESSION['grado_sec'] = "";
	$_SESSION['cod_doc_sec'] = "";     
	$_SESSION['cod_mat_sec'] = ""; 
	$_SESSION['seccion_sec'] = ""; 
	$_SESSION['lapso_sec'] = ""; 
	$_SESSION['tiplap_sec'] = ""; 

	
	while($row = $result->fetch_assoc()) {      


		$_SESSION['alumno'] = $row['alumno'];
		$_SESSION['docente'] = $row['docente'];		
		$_SESSION['notas'] = $row['notas'];
		$_SESSION['lapso'] = $row['lapso'];
		$_SESSION['lismat'] = $row['lismat'];
		$_SESSION['seccion'] = $row['seccion'];
		$_SESSION['tipos_lapso'] = $row['tipos_lapso'];
		$_SESSION['nota'] = $row['nota'];
		$_SESSION['notas_guardar'] = $row['notas_guardar'];
		$_SESSION['notas_modificar'] = $row['notas_modificar'];
		$_SESSION['notas_borrar'] = $row['notas_borrar'];
		$_SESSION['user'] = $row['user'];
		$_SESSION['user_clave'] = $row['user_clave'];
		$_SESSION['auditoria'] = $row['auditoria'];  
		$_SESSION['actas'] = $row['actas'];  
		$_SESSION['historiales'] = $row['historiales'];  
		$_SESSION['agregar_seccion'] = $row['agregar_seccion'];     
		$_SESSION['inscribir_materia'] = $row['inscribir_materia'];
		$_SESSION['copiar_seccion'] = $row['copiar_seccion'];
		$_SESSION['eliminar_seccion'] = $row['eliminar_seccion'];
		$_SESSION['aula'] = $row['aula'];
		$_SESSION['horas'] = $row['horas'];

		$_SESSION['electivas'] = $row['electivas'];
		$_SESSION['cambiar_docente'] = $row['cambiar_docente'];
		$_SESSION['cambiar_lapso'] = $row['cambiar_lapso'];
		$_SESSION['cambiar_seccion'] = $row['cambiar_seccion'];
		$_SESSION['cambiar_materia'] = $row['cambiar_materia'];
        $_SESSION['desactivar_alumnos'] = $row['desactivar_alumnos'];
        
        //$_SESSION['planificacion'] = $row['planificacion'];
       

	}

	header("Location: principal.php");

	require ("aud.php");
	auditar("0A");


} else {
	$_SESSION['loggedin'] = false;
	header("Location: index.html");
}
$conn->close();


?>
