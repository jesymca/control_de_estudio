<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
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




		
		$LAPSO=$_POST["lapso"];
		$MATERIA=$_POST["cod_mat"];        
		$TIPO =$_POST["tiplap"];
		$cod_doc=$_POST["cod_doc"];		
		$SECCION=$_POST["seccion"];
		
		$CARRERA=substr($MATERIA, 0, 1);


		switch ($CARRERA)  {
			case "M":
			$Carrera_a1="P.N.F. MECANICA";
			$Carrera_a2="MECANICA";
			break;
			case "T":
			$Carrera_a1="P.N.F. MANTENIMIENTO";
			$Carrera_a2="MANTENIMIENTO";
			break;
			case "E":
			$Carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
			$Carrera_a2="MATERIALES";
			break;
			case "I":
			$Carrera_a1="P.N.F. INFORMATICA";
			$Carrera_a2="INFORMATICA";
			break;
			case "G":
			$Carrera_a1="P.N.F. TURISMO";
			$Carrera_a2="TURISMO";
			break;   
			case "O":
			$Carrera_a1="P.I.F. MECANICA TERMICA";
			$Carrera_a2="TERMICA";
			break;
			case "A":
			$Carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
			$Carrera_a2="AUTOMOTRIZ";
			break;
			case "C":
			$Carrera_a1="P.N.F. CIENCIAS FISCALES";
			$Carrera_a2="CIENCIAS FISCALES";
			break;
		}
		
     
        
		include "db.php";
		$sql = "UPDATE `alumno`,`notas` SET `alumno`.actividad=1 WHERE`notas`.`cod_mat`='".$MATERIA."' AND `notas`.`lapso`='".$LAPSO."' AND `notas`.`seccion`='".$SECCION."' AND `notas`.cod_doc='".$cod_doc."' AND `alumno`.cedula=`notas`.codigo";
		$resultado = $conn->query($sql);

        
        header("Location: activar seccion.php");
       
?>
