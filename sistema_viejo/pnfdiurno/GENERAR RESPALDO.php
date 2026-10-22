<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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

$pathname="C:temp";

if (is_dir($pathname) || empty($pathname)) {
        
   }else{
   	  mkdir($pathname);   	  
   }


   date_default_timezone_set('America/Caracas');
   $fechaActual = date('d-m-Y');  

$pathname2=$pathname.chr(47).$fechaActual;

if (is_dir($pathname2) || empty($pathname2)) {
        
   }else{
   	  mkdir($pathname2);   	  
   }




tabla_notas("notas",$fechaActual,$pathname);
function tabla_notas($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `notas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

   	fwrite($archivo,"CREATE TABLE `notas` (". PHP_EOL);
  	fwrite($archivo,"  `id` int(20) NOT NULL AUTO_INCREMENT,". PHP_EOL);
  	fwrite($archivo,"  `codigo` varchar(13) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `cod_mat` char(8) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `carrera` char(1) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `nota` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `lapso` char(6) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `tiplap` char(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `cod_doc` int(11) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `cod_usu` varchar(15) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `acu` double DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `seccion` char(3) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `electiva` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `fecha` char(10) NOT NULL". PHP_EOL);
  	fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `notas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."codigo".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."carrera".chr(96).", ".chr(96)."nota".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."tiplap".chr(96).", ".chr(96)."cod_doc".chr(96).", ".chr(96)."cod_usu".chr(96).", ".chr(96)."acu".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."electiva".chr(96).", ".chr(96)."fecha".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$codigo = $fila['codigo'];
			$cod_mat = $fila['cod_mat'];
			$carrera = $fila['carrera'];
			$nota = $fila['nota'];
			$lapso = $fila['lapso'];
			$tiplap = $fila['tiplap'];
			$cod_doc = $fila['cod_doc'];
			$cod_usu = $fila['cod_usu'];
			$acu = $fila['acu'];
			$seccion = $fila['seccion'];
			$electiva = $fila['electiva'];
			$fecha = $fila['fecha'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$codigo."', '".$cod_mat."', '".$carrera."', '".$nota."', '".$lapso."', '".$tiplap."', '".$cod_doc."', '".$cod_usu."', '".$acu."', '".$seccion."', '".$electiva."', '".$fecha."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$codigo."', '".$cod_mat."', '".$carrera."', '".$nota."', '".$lapso."', '".$tiplap."', '".$cod_doc."', '".$cod_usu."', '".$acu."', '".$seccion."', '".$electiva."', '".$fecha."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo "Se genero los siguientes arquivos sql para respaldo:"."<br>";	
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";		
}


tabla_agregarseccion("agregarseccion",$fechaActual,$pathname);
function tabla_agregarseccion($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `agregarseccion`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);


   	fwrite($archivo,"CREATE TABLE `agregarseccion` (". PHP_EOL);
  	fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
  	fwrite($archivo,"  `pensum` char(3) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `cod_mat` char(5) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `seccion` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `cod_doc` char(11) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `lapso` char(6) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `aula` char(4) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `descrip` char(20) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `hora` char(7) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `tipo` char(1) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `electiva` char(2) NOT NULL". PHP_EOL);
   	fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `agregarseccion`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."pensum".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."cod_doc".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."aula".chr(96).", ".chr(96)."descrip".chr(96).", ".chr(96)."hora".chr(96).", ".chr(96)."tipo".chr(96).", ".chr(96)."electiva".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$pensum = $fila['pensum'];
			$cod_mat = $fila['cod_mat'];
			$seccion = $fila['seccion'];
			$cod_doc = $fila['cod_doc'];
			$lapso = $fila['lapso'];
			$aula = $fila['aula'];
			$descrip = $fila['descrip'];
			$hora = $fila['hora'];
			$tipo = $fila['tipo'];
			$electiva = $fila['electiva'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$pensum."', '".$cod_mat."', '".$seccion."', '".$cod_doc."', '".$lapso."', '".$aula."', '".$descrip."', '".$hora."', '".$tipo."', '".$electiva."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$pensum."', '".$cod_mat."', '".$seccion."', '".$cod_doc."', '".$lapso."', '".$aula."', '".$descrip."', '".$hora."', '".$tipo."', '".$electiva."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}


tabla_alumno("alumno",$fechaActual,$pathname);
function tabla_alumno($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
     
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `alumno`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
 
   	fwrite($archivo,"CREATE TABLE `alumno` (". PHP_EOL);
  	fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
  	fwrite($archivo,"  `codigo` varchar(13) DEFAULT NULL UNIQUE KEY,". PHP_EOL);
  	fwrite($archivo,"  `cedula` varchar(13) DEFAULT NULL UNIQUE KEY,". PHP_EOL);
  	fwrite($archivo,"  `nombre` varchar(100) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `carrera` varchar(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `mencion` varchar(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `plan` varchar(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `pensum` varchar(3) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `actividad` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `sexo` char(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `edocivil` char(15) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `lugar` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `municipio` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `estado` varchar(20) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `procedenci` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `fechanac` char(15) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `edad` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `direccion` varchar(40) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `telefonoh` varchar(12) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `telefonoc` varchar(12) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `telefonot` varchar(12) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `email` varchar(30) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `tipingreso` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `mencionbac` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `anogra_bac` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `indiceopsu` double DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `tipinspro` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `codinspro` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `nivelsocio` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ingreso` char(10) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `nuevo` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `semestre` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_cug` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_eqg` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_ing` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_apg` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_reg` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_ge` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_ap` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_eq` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_fa` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uc_ins` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `mat_ins` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `turno_ins` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ultlapalu` varchar(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `penlapalu` varchar(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `numlapalu` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ncsemant` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `iras` char(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `optculesc` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `posgrad` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `marca` varchar(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `graduad` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `num_titulo` varchar(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `promocion` varchar(10) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `fe_gr_alu` char(15) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `irapromo` char(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `egreso` varchar(6) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `num_est` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ubicacion` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `trab_esp` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `documento` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `pasantia` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `uccurul` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ucaprul` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `ira` double DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `iraa` double DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  `maxuccur` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` maxuccur2` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` liso` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` r25p` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ap1` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ap11` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` desertor` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi8v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi7v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi6v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi5v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi4v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` curasi3v` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` grupo` varchar(2) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` turno` char(15) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` tip_hor` varchar(1) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` alu_pre` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` trabajo` char(2) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ret_act` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` rei_act` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` cam_sp` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ret_sp` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` rei_sp` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` regsem` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` rezagado` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` cambio` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` nivel` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` cod_usu` varchar(3) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` solicitud` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` pen_sol` varchar(3) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` cam_apr` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` eficiencia` double DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` clave` varchar(4) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` planpago` varchar(10) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` beca` char(2) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` iserialdis` varchar(12) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` iusuario` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ireceptor` varchar(25) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ihora` varchar(8) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ifecha` date DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ipensum` varchar(3) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` rusnies` varchar(15) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` r_cupo` int(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` discapacid` char(11) DEFAULT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` tomo` char(15) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` folio` char(15) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` pnf` int(1) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` trayecto` int(1) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fcedula` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` inscripmilt` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` ftitulo` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fcerfidicado` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fnotas` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fdosfotos` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fpinscrip` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  ` fdepbanc` char(2) NOT NULL,". PHP_EOL);
  	fwrite($archivo,"  `fnacimie` char(2) NOT NULL". PHP_EOL);
  	fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL); 
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `alumno`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."codigo".chr(96).", ".chr(96)."cedula".chr(96).", ".chr(96)."nombre".chr(96).", ".chr(96)."carrera".chr(96).", ".chr(96)."mencion".chr(96).", ".chr(96)."plan".chr(96).", ".chr(96)."pensum".chr(96).", ".chr(96)."actividad".chr(96).", ".chr(96)."sexo".chr(96).", ".chr(96)."edocivil".chr(96).", ".chr(96)."lugar".chr(96).", ".chr(96)."municipio".chr(96).", ".chr(96)."estado".chr(96).", ".chr(96)."procedenci".chr(96).", ".chr(96)."fechanac".chr(96).", ".chr(96)."edad".chr(96).", ".chr(96)."direccion".chr(96).", ".chr(96)."telefonoh".chr(96).", ".chr(96)."telefonoc".chr(96).", ".chr(96)."telefonot".chr(96).", ".chr(96)."email".chr(96).", ".chr(96)."tipingreso".chr(96).", ".chr(96)."mencionbac".chr(96).", ".chr(96)."anogra_bac".chr(96).", ".chr(96)."indiceopsu".chr(96).", ".chr(96)."tipinspro".chr(96).", ".chr(96)."codinspro".chr(96).", ".chr(96)."nivelsocio".chr(96).", ".chr(96)."ingreso".chr(96).", ".chr(96)."nuevo".chr(96).", ".chr(96)."semestre".chr(96).", ".chr(96)."uc_cug".chr(96).", ".chr(96)."uc_eqg".chr(96).", ".chr(96)."uc_ing".chr(96).", ".chr(96)."uc_apg".chr(96).", ".chr(96)."uc_reg".chr(96).", ".chr(96)."uc_ge".chr(96).", ".chr(96)."uc_ap".chr(96).", ".chr(96)."uc_eq".chr(96).", ".chr(96)."uc_fa".chr(96).", ".chr(96)."uc_ins".chr(96).", ".chr(96)."mat_ins".chr(96).", ".chr(96)."turno_ins".chr(96).", ".chr(96)."ultlapalu".chr(96).", ".chr(96)."penlapalu".chr(96).", ".chr(96)."numlapalu".chr(96).", ".chr(96)."ncsemant".chr(96).", ".chr(96)."iras".chr(96).", ".chr(96)."optculesc".chr(96).", ".chr(96)."posgrad".chr(96).", ".chr(96)."marca".chr(96).", ".chr(96)."graduad".chr(96).", ".chr(96)."num_titulo".chr(96).", ".chr(96)."promocion".chr(96).", ".chr(96)."fe_gr_alu".chr(96).", ".chr(96)."irapromo".chr(96).", ".chr(96)."egreso".chr(96).", ".chr(96)."num_est".chr(96).", ".chr(96)."ubicacion".chr(96).", ".chr(96)."trab_esp".chr(96).", ".chr(96)."documento".chr(96).", ".chr(96)."pasantia".chr(96).", ".chr(96)."uccurul".chr(96).", ".chr(96)."ucaprul".chr(96).", ".chr(96)."ira".chr(96).", ".chr(96)."iraa".chr(96).", ".chr(96)."maxuccur".chr(96).", ".chr(96)."maxuccur2".chr(96).", ".chr(96)."liso".chr(96).", ".chr(96)."r25p".chr(96).", ".chr(96)."ap1".chr(96).", ".chr(96)."ap11".chr(96).", ".chr(96)."desertor".chr(96).", ".chr(96)."curasi8v".chr(96).", ".chr(96)."curasi7v".chr(96).", ".chr(96)."curasi6v".chr(96).", ".chr(96)."curasi5v".chr(96).", ".chr(96)."curasi4v".chr(96).", ".chr(96)."curasi3v".chr(96).", ".chr(96)."grupo".chr(96).", ".chr(96)."turno".chr(96).", ".chr(96)."tip_hor".chr(96).", ".chr(96)."alu_pre".chr(96).", ".chr(96)."trabajo".chr(96).", ".chr(96)."ret_act".chr(96).", ".chr(96)."rei_act".chr(96).", ".chr(96)."cam_sp".chr(96).", ".chr(96)."ret_sp".chr(96).", ".chr(96)."rei_sp".chr(96).", ".chr(96)."regsem".chr(96).", ".chr(96)."rezagado".chr(96).", ".chr(96)."cambio".chr(96).", ".chr(96)."nivel".chr(96).", ".chr(96)."cod_usu".chr(96).", ".chr(96)."solicitud".chr(96).", ".chr(96)."pen_sol".chr(96).", ".chr(96)."cam_apr".chr(96).", ".chr(96)."eficiencia".chr(96).", ".chr(96)."clave".chr(96).", ".chr(96)."planpago".chr(96).", ".chr(96)."beca".chr(96).", ".chr(96)."iserialdis".chr(96).", ".chr(96)."iusuario".chr(96).", ".chr(96)."ireceptor".chr(96).", ".chr(96)."ihora".chr(96).", ".chr(96)."ifecha".chr(96).", ".chr(96)."ipensum".chr(96).", ".chr(96)."rusnies".chr(96).", ".chr(96)."r_cupo".chr(96).", ".chr(96)."discapacid".chr(96).", ".chr(96)."tomo".chr(96).", ".chr(96)."folio".chr(96).", ".chr(96)."pnf".chr(96).", ".chr(96)."trayecto".chr(96).", ".chr(96)."fcedula".chr(96).", ".chr(96)."inscripmilt".chr(96).", ".chr(96)."ftitulo".chr(96).", ".chr(96)."fcerfidicado".chr(96).", ".chr(96)."fnotas".chr(96).", ".chr(96)."fdosfotos".chr(96).", ".chr(96)."fpinscrip".chr(96).", ".chr(96)."fdepbanc".chr(96).", ".chr(96)."fnacimie".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$codigo = $fila['codigo'];
			$cedula = $fila['cedula'];
			$nombre = $fila['nombre'];
			$carrera = $fila['carrera'];
			$mencion = $fila['mencion'];
			$plan = $fila['plan'];
			$pensum = $fila['pensum'];
			$actividad = $fila['actividad'];
			$sexo = $fila['sexo'];
			$edocivil = $fila['edocivil'];
			$lugar = $fila['lugar'];
			$municipio = $fila['municipio'];
			$estado = $fila['estado'];
			$procedenci = $fila['procedenci'];
			$fechanac = $fila['fechanac'];
			$edad = $fila['edad'];
			$direccion = $fila['direccion'];
			$telefonoh = $fila['telefonoh'];
			$telefonoc = $fila['telefonoc'];
			$telefonot = $fila['telefonot'];
			$email = $fila['email'];
			$tipingreso = $fila['tipingreso'];
			$mencionbac = $fila['mencionbac'];
			$anogra_bac = $fila['anogra_bac'];
			$indiceopsu = $fila['indiceopsu'];
			$tipinspro = $fila['tipinspro'];
			$codinspro = $fila['codinspro'];
			$nivelsocio = $fila['nivelsocio'];
			$ingreso = $fila['ingreso'];
			$nuevo = $fila['nuevo'];
			$semestre = $fila['semestre'];
			$uc_cug = $fila['uc_cug'];
			$uc_eqg = $fila['uc_eqg'];
			$uc_ing = $fila['uc_ing'];
			$uc_apg = $fila['uc_apg'];
			$uc_reg = $fila['uc_reg'];
			$uc_ge = $fila['uc_ge'];
			$uc_ap = $fila['uc_ap'];
			$uc_eq = $fila['uc_eq'];
			$uc_fa = $fila['uc_fa'];
			$uc_ins = $fila['uc_ins'];
			$mat_ins = $fila['mat_ins'];
			$turno_ins = $fila['turno_ins'];
			$ultlapalu = $fila['ultlapalu'];
			$penlapalu = $fila['penlapalu'];
			$numlapalu = $fila['numlapalu'];
			$ncsemant = $fila['ncsemant'];
			$iras = $fila['iras'];
			$optculesc = $fila['optculesc'];
			$posgrad = $fila['posgrad'];
			$marca = $fila['marca'];
			$graduad = $fila['graduad'];
			$num_titulo = $fila['num_titulo'];
			$promocion = $fila['promocion'];
			$fe_gr_alu = $fila['fe_gr_alu'];
			$irapromo = $fila['irapromo'];
			$egreso = $fila['egreso'];
			$num_est = $fila['num_est'];
			$ubicacion = $fila['ubicacion'];
			$trab_esp = $fila['trab_esp'];
			$documento = $fila['documento'];
			$pasantia = $fila['pasantia'];
			$uccurul = $fila['uccurul'];
			$ucaprul = $fila['ucaprul'];
			$ira = $fila['ira'];
			$iraa = $fila['iraa'];
			$maxuccur = $fila['maxuccur'];
			$maxuccur2 = $fila['maxuccur2'];
			$liso = $fila['liso'];
			$r25p = $fila['r25p'];
			$ap1 = $fila['ap1'];
			$ap11 = $fila['ap11'];
			$desertor = $fila['desertor'];
			$curasi8v = $fila['curasi8v'];
			$curasi7v = $fila['curasi7v'];
			$curasi6v = $fila['curasi6v'];
			$curasi5v = $fila['curasi5v'];
			$curasi4v = $fila['curasi4v'];
			$curasi3v = $fila['curasi3v'];
			$grupo = $fila['grupo'];
			$turno = $fila['turno'];
			$tip_hor = $fila['tip_hor'];
			$alu_pre = $fila['alu_pre'];
			$trabajo = $fila['trabajo'];
			$ret_act = $fila['ret_act'];
			$rei_act = $fila['rei_act'];
			$cam_sp = $fila['cam_sp'];
			$ret_sp = $fila['ret_sp'];
			$rei_sp = $fila['rei_sp'];
			$regsem = $fila['regsem'];
			$rezagado = $fila['rezagado'];
			$cambio = $fila['cambio'];
			$nivel = $fila['nivel'];
			$cod_usu = $fila['cod_usu'];
			$solicitud = $fila['solicitud'];
			$pen_sol = $fila['pen_sol'];
			$cam_apr = $fila['cam_apr'];
			$eficiencia = $fila['eficiencia'];
			$clave = $fila['clave'];
			$planpago = $fila['planpago'];
			$beca = $fila['beca'];
			$iserialdis = $fila['iserialdis'];
			$iusuario = $fila['iusuario'];
			$ireceptor = $fila['ireceptor'];
			$ihora = $fila['ihora'];
			$ifecha = $fila['ifecha'];
			$ipensum = $fila['ipensum'];
			$rusnies = $fila['rusnies'];
			$r_cupo = $fila['r_cupo'];
			$discapacid = $fila['discapacid'];
			$tomo = $fila['tomo'];
			$folio = $fila['folio'];
			$pnf = $fila['pnf'];
			$trayecto = $fila['trayecto'];
			$fcedula = $fila['fcedula'];
			$inscripmilt = $fila['inscripmilt'];
			$ftitulo = $fila['ftitulo'];
			$fcerfidicado = $fila['fcerfidicado'];
			$fnotas = $fila['fnotas'];
			$fdosfotos = $fila['fdosfotos'];
			$fpinscrip = $fila['fpinscrip'];
			$fdepbanc = $fila['fdepbanc'];
			$fnacimie = $fila['fnacimie'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$codigo."', '".$cedula."', '".$nombre."', '".$carrera."', '".$mencion."', '".$plan."', '".$pensum."', '".$actividad."', '".$sexo."', '".$edocivil."', '".$lugar."', '".$municipio."', '".$estado."', '".$procedenci."', '".$fechanac."', '".$edad."', '".$direccion."', '".$telefonoh."', '".$telefonoc."', '".$telefonot."', '".$email."', '".$tipingreso."', '".$mencionbac."', '".$anogra_bac."', '".$indiceopsu."', '".$tipinspro."', '".$codinspro."', '".$nivelsocio."', '".$ingreso."', '".$nuevo."', '".$semestre."', '".$uc_cug."', '".$uc_eqg."', '".$uc_ing."', '".$uc_apg."', '".$uc_reg."', '".$uc_ge."', '".$uc_ap."', '".$uc_eq."', '".$uc_fa."', '".$uc_ins."', '".$mat_ins."', '".$turno_ins."', '".$ultlapalu."', '".$penlapalu."', '".$numlapalu."', '".$ncsemant."', '".$iras."', '".$optculesc."', '".$posgrad."', '".$marca."', '".$graduad."', '".$num_titulo."', '".$promocion."', '".$fe_gr_alu."', '".$irapromo."', '".$egreso."', '".$num_est."', '".$ubicacion."', '".$trab_esp."', '".$documento."', '".$pasantia."', '".$uccurul."', '".$ucaprul."', '".$ira."', '".$iraa."', '".$maxuccur."', '".$maxuccur2."', '".$liso."', '".$r25p."', '".$ap1."', '".$ap11."', '".$desertor."', '".$curasi8v."', '".$curasi7v."', '".$curasi6v."', '".$curasi5v."', '".$curasi4v."', '".$curasi3v."', '".$grupo."', '".$turno."', '".$tip_hor."', '".$alu_pre."', '".$trabajo."', '".$ret_act."', '".$rei_act."', '".$cam_sp."', '".$ret_sp."', '".$rei_sp."', '".$regsem."', '".$rezagado."', '".$cambio."', '".$nivel."', '".$cod_usu."', '".$solicitud."', '".$pen_sol."', '".$cam_apr."', '".$eficiencia."', '".$clave."', '".$planpago."', '".$beca."', '".$iserialdis."', '".$iusuario."', '".$ireceptor."', '".$ihora."', '".$ifecha."', '".$ipensum."', '".$rusnies."', '".$r_cupo."', '".$discapacid."', '".$tomo."', '".$folio."', '".$pnf."', '".$trayecto."', '".$fcedula."', '".$inscripmilt."', '".$ftitulo."', '".$fcerfidicado."', '".$fnotas."', '".$fdosfotos."', '".$fpinscrip."', '".$fdepbanc."', '".$fnacimie."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$codigo."', '".$cedula."', '".$nombre."', '".$carrera."', '".$mencion."', '".$plan."', '".$pensum."', '".$actividad."', '".$sexo."', '".$edocivil."', '".$lugar."', '".$municipio."', '".$estado."', '".$procedenci."', '".$fechanac."', '".$edad."', '".$direccion."', '".$telefonoh."', '".$telefonoc."', '".$telefonot."', '".$email."', '".$tipingreso."', '".$mencionbac."', '".$anogra_bac."', '".$indiceopsu."', '".$tipinspro."', '".$codinspro."', '".$nivelsocio."', '".$ingreso."', '".$nuevo."', '".$semestre."', '".$uc_cug."', '".$uc_eqg."', '".$uc_ing."', '".$uc_apg."', '".$uc_reg."', '".$uc_ge."', '".$uc_ap."', '".$uc_eq."', '".$uc_fa."', '".$uc_ins."', '".$mat_ins."', '".$turno_ins."', '".$ultlapalu."', '".$penlapalu."', '".$numlapalu."', '".$ncsemant."', '".$iras."', '".$optculesc."', '".$posgrad."', '".$marca."', '".$graduad."', '".$num_titulo."', '".$promocion."', '".$fe_gr_alu."', '".$irapromo."', '".$egreso."', '".$num_est."', '".$ubicacion."', '".$trab_esp."', '".$documento."', '".$pasantia."', '".$uccurul."', '".$ucaprul."', '".$ira."', '".$iraa."', '".$maxuccur."', '".$maxuccur2."', '".$liso."', '".$r25p."', '".$ap1."', '".$ap11."', '".$desertor."', '".$curasi8v."', '".$curasi7v."', '".$curasi6v."', '".$curasi5v."', '".$curasi4v."', '".$curasi3v."', '".$grupo."', '".$turno."', '".$tip_hor."', '".$alu_pre."', '".$trabajo."', '".$ret_act."', '".$rei_act."', '".$cam_sp."', '".$ret_sp."', '".$rei_sp."', '".$regsem."', '".$rezagado."', '".$cambio."', '".$nivel."', '".$cod_usu."', '".$solicitud."', '".$pen_sol."', '".$cam_apr."', '".$eficiencia."', '".$clave."', '".$planpago."', '".$beca."', '".$iserialdis."', '".$iusuario."', '".$ireceptor."', '".$ihora."', '".$ifecha."', '".$ipensum."', '".$rusnies."', '".$r_cupo."', '".$discapacid."', '".$tomo."', '".$folio."', '".$pnf."', '".$trayecto."', '".$fcedula."', '".$inscripmilt."', '".$ftitulo."', '".$fcerfidicado."', '".$fnotas."', '".$fdosfotos."', '".$fpinscrip."', '".$fdepbanc."', '".$fnacimie."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}

tabla_docente("docente",$fechaActual,$pathname);
function tabla_docente($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `docente`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `docente` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `cod_doc` int(11) DEFAULT NULL UNIQUE KEY,". PHP_EOL);
    fwrite($archivo,"  `cedula` varchar(10) DEFAULT NULL UNIQUE KEY,". PHP_EOL);
    fwrite($archivo,"  `nombre` varchar(30) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `condicion` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `depart` varchar(1) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `sexo` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `fechanac` date DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `titulo_c` varchar(4) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `titulo_l` varchar(25) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `tipo` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `ingreso` date DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `categoria` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `dedicacion` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `telefono` varchar(30) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `asignatura` varchar(2) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `horas_ad` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `horas_do` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `observa` varchar(60) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `actividad` int(11) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `turno` int(11) DEFAULT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `docente`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."cod_doc".chr(96).", ".chr(96)."cedula".chr(96).", ".chr(96)."nombre".chr(96).", ".chr(96)."condicion".chr(96).", ".chr(96)."depart".chr(96).", ".chr(96)."sexo".chr(96).", ".chr(96)."fechanac".chr(96).", ".chr(96)."titulo_c".chr(96).", ".chr(96)."titulo_l".chr(96).", ".chr(96)."tipo".chr(96).", ".chr(96)."ingreso".chr(96).", ".chr(96)."categoria".chr(96).", ".chr(96)."dedicacion".chr(96).", ".chr(96)."telefono".chr(96).", ".chr(96)."asignatura".chr(96).", ".chr(96)."horas_ad".chr(96).", ".chr(96)."horas_do".chr(96).", ".chr(96)."observa".chr(96).", ".chr(96)."actividad".chr(96).", ".chr(96)."turno".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$cod_doc = $fila['cod_doc'];
			$cedula = $fila['cedula'];
			$nombre = $fila['nombre'];
			$condicion = $fila['condicion'];
			$depart = $fila['depart'];
			$sexo = $fila['sexo'];
			$fechanac = $fila['fechanac'];
			$titulo_c = $fila['titulo_c'];
			$titulo_l = $fila['titulo_l'];
			$tipo = $fila['tipo'];
			$ingreso = $fila['ingreso'];
			$categoria = $fila['categoria'];
			$dedicacion = $fila['dedicacion'];
			$telefono = $fila['telefono'];
			$asignatura = $fila['asignatura'];
			$horas_ad = $fila['horas_ad'];
			$horas_do = $fila['horas_do'];
			$observa = $fila['observa'];
			$actividad = $fila['actividad'];
			$turno = $fila['turno'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$cod_doc."', '".$cedula."', '".$nombre."', '".$condicion."', '".$depart."', '".$sexo."', '".$fechanac."', '".$titulo_c."', '".$titulo_l."', '".$tipo."', '".$ingreso."', '".$categoria."', '".$dedicacion."', '".$telefono."', '".$asignatura."', '".$horas_ad."', '".$horas_do."', '".$observa."', '".$actividad."', '".$turno."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$cod_doc."', '".$cedula."', '".$nombre."', '".$condicion."', '".$depart."', '".$sexo."', '".$fechanac."', '".$titulo_c."', '".$titulo_l."', '".$tipo."', '".$ingreso."', '".$categoria."', '".$dedicacion."', '".$telefono."', '".$asignatura."', '".$horas_ad."', '".$horas_do."', '".$observa."', '".$actividad."', '".$turno."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}



tabla_aula("aula",$fechaActual,$pathname);
function tabla_aula($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);


    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `aula`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `aula` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `descrip` char(20) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `aula` char(4) DEFAULT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `aula`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."descrip".chr(96).", ".chr(96)."aula".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$descrip = $fila['descrip'];
			$aula = $fila['aula'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$descrip."', '".$aula."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$descrip."', '".$aula."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}


tabla_horas("horas",$fechaActual,$pathname);
function tabla_horas($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);



    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `horas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `horas` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `hora` char(7) DEFAULT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `horas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."hora".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$hora = $fila['hora'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$hora."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$hora."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}


tabla_lapso("lapso",$fechaActual,$pathname);
function tabla_lapso($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `lapso`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);


    fwrite($archivo,"CREATE TABLE `lapso` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `lapso` varchar(6) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `descrip` varchar(50) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `carrera` char(10) NOT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `lapso`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."lapso".chr(96).", ".chr(96)."descrip".chr(96).", ".chr(96)."carrera".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$lapso = $fila['lapso'];
			$descrip = $fila['descrip'];
			$carrera = $fila['carrera'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$lapso."', '".$descrip."', '".$carrera."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$lapso."', '".$descrip."', '".$carrera."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}


tabla_lismat("lismat",$fechaActual,$pathname);
function tabla_lismat($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `lismat`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `lismat` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `pensum` varchar(3) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `cod_mat` varchar(7) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `descrip2` varchar(100) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `creditos` int(2) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `aprobatori` int(2) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `semestre` char(2) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `trayecto` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `divicion` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `nota` char(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `cod_mat_libro_rector` char(20) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `cod_mat_ant` char(10) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `grado` char(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `electiva` int(1) NOT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);   
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `lismat`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."pensum".chr(96).", ".chr(96)."cod_mat".chr(96).", ".chr(96)."descrip2".chr(96).", ".chr(96)."creditos".chr(96).", ".chr(96)."aprobatori".chr(96).", ".chr(96)."semestre".chr(96).", ".chr(96)."trayecto".chr(96).", ".chr(96)."divicion".chr(96).", ".chr(96)."nota".chr(96).", ".chr(96)."cod_mat_libro_rector".chr(96).", ".chr(96)."cod_mat_ant".chr(96).", ".chr(96)."grado".chr(96).", ".chr(96)."electiva".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$pensum = $fila['pensum'];
			$cod_mat = $fila['cod_mat'];
			$descrip2 = $fila['descrip2'];
			$creditos = $fila['creditos'];
			$aprobatori = $fila['aprobatori'];
			$semestre = $fila['semestre'];
			$trayecto = $fila['trayecto'];
			$divicion = $fila['divicion'];
			$nota = $fila['nota'];
			$cod_mat_libro_rector = $fila['cod_mat_libro_rector'];
			$cod_mat_ant = $fila['cod_mat_ant'];
			$grado = $fila['grado'];
			$electiva = $fila['electiva'];
			
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$pensum."', '".$cod_mat."', '".$descrip2."', '".$creditos."', '".$aprobatori."', '".$semestre."', '".$trayecto."', '".$divicion."', '".$nota."', '".$cod_mat_libro_rector."', '".$cod_mat_ant."', '".$grado."', '".$electiva."'),". PHP_EOL); 
			}else{
				fwrite($archivo,"('".$pensum."', '".$cod_mat."', '".$descrip2."', '".$creditos."', '".$aprobatori."', '".$semestre."', '".$trayecto."', '".$divicion."', '".$nota."', '".$cod_mat_libro_rector."', '".$cod_mat_ant."', '".$grado."', '".$electiva."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}

tabla_pensum("pensum",$fechaActual,$pathname);
function tabla_pensum($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `pensum`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `pensum` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `pensum` char(3) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `descripcion` char(30) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `descripcion2` char(20) NOT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `pensum`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."pensum".chr(96).", ".chr(96)."descripcion".chr(96).", ".chr(96)."descripcion2".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$pensum = $fila['pensum'];
			$descripcion = $fila['descripcion'];
			$descripcion2 = $fila['descripcion2'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$pensum."', '".$descripcion."', '".$descripcion2."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$pensum."', '".$descripcion."', '".$descripcion2."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}

tabla_seccion("seccion",$fechaActual,$pathname);
function tabla_seccion($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `seccion`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `seccion` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `seccion` char(3) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `descripcion` char(20) NOT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `seccion`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);


	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."seccion".chr(96).", ".chr(96)."descripcion".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$seccion = $fila['seccion'];
			$descripcion = $fila['descripcion'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$seccion."', '".$descripcion."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$seccion."', '".$descripcion."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}



tabla_user("user",$fechaActual,$pathname);
function tabla_user($tabla,$fechaActual,$pathname)
{
	$archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
	fwrite($archivo,"". PHP_EOL);
	fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `user`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `user` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `nombre` char(30) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `login` char(30) DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `clave` char(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,". PHP_EOL);
    fwrite($archivo,"  `alumno` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `docente` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `notas` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `notas_guardar` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `notas_modificar` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `notas_borrar` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `lapso` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `lismat` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `seccion` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `tipos_lapso` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `nota` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `user` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `user_clave` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `auditoria` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `actas` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `historiales` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `agregar_seccion` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `inscribir_materia` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `copiar_seccion` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `eliminar_seccion` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `horas` int(1) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `aula` int(1) NOT NULL". PHP_EOL);
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
  	fwrite($archivo,"". PHP_EOL);
  	fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
   
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `user`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    
	fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."nombre".chr(96).", ".chr(96)."login".chr(96).", ".chr(96)."clave".chr(96).", ".chr(96)."alumno".chr(96).", ".chr(96)."docente".chr(96).", ".chr(96)."notas".chr(96).", ".chr(96)."notas_guardar".chr(96).", ".chr(96)."notas_modificar".chr(96).", ".chr(96)."notas_borrar".chr(96).", ".chr(96)."lapso".chr(96).", ".chr(96)."lismat".chr(96).", ".chr(96)."seccion".chr(96).", ".chr(96)."tipos_lapso".chr(96).", ".chr(96)."nota".chr(96).", ".chr(96)."user".chr(96).", ".chr(96)."user_clave".chr(96).", ".chr(96)."auditoria".chr(96).", ".chr(96)."actas".chr(96).", ".chr(96)."historiales".chr(96).", ".chr(96)."agregar_seccion".chr(96).", ".chr(96)."inscribir_materia".chr(96).", ".chr(96)."copiar_seccion".chr(96).", ".chr(96)."eliminar_seccion".chr(96).", ".chr(96)."horas".chr(96).", ".chr(96)."aula".chr(96).") VALUES". PHP_EOL);
	include "db.php";
	$sql = "SHOW COLUMNS FROM ".$tabla;
	$contador1=0;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre[$contador1]=$fila["Field"]."<br>";
			$contador1++;
		}
	}
	$sql = "SELECT DISTINCT * FROM ".$tabla;
	$resultado = $conn->query($sql);
	$contador=1;
	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {
			$nombre = $fila['nombre'];
			$login = $fila['login'];
			$clave = $fila['clave'];
			$alumno = $fila['alumno'];
			$docente = $fila['docente'];
			$notas = $fila['notas'];
			$notas_guardar = $fila['notas_guardar'];
			$notas_modificar = $fila['notas_modificar'];
			$notas_borrar = $fila['notas_borrar'];
			$lapso = $fila['lapso'];
			$lismat = $fila['lismat'];
			$seccion = $fila['seccion'];
			$tipos_lapso = $fila['tipos_lapso'];
			$nota = $fila['nota'];
			$user = $fila['user'];
			$user_clave = $fila['user_clave'];
			$auditoria = $fila['auditoria'];
			$actas = $fila['actas'];
			$historiales = $fila['historiales'];
			$agregar_seccion = $fila['agregar_seccion'];
			$inscribir_materia = $fila['inscribir_materia'];
			$copiar_seccion = $fila['copiar_seccion'];
			$eliminar_seccion = $fila['eliminar_seccion'];
			$horas = $fila['horas'];
			$aula = $fila['aula'];
			$cant=$resultado->num_rows;
			if($contador<>$resultado->num_rows){
				fwrite($archivo,"('".$nombre."', '".$login."', '".$clave."', '".$alumno."', '".$docente."', '".$notas."', '".$notas_guardar."', '".$notas_modificar."', '".$notas_borrar."', '".$lapso."', '".$lismat."', '".$seccion."', '".$tipos_lapso."', '".$nota."', '".$user."', '".$user_clave."', '".$auditoria."', '".$actas."', '".$historiales."', '".$agregar_seccion."', '".$inscribir_materia."', '".$copiar_seccion."', '".$eliminar_seccion."', '".$horas."', '".$aula."'),". PHP_EOL);
			}else{
				fwrite($archivo,"('".$nombre."', '".$login."', '".$clave."', '".$alumno."', '".$docente."', '".$notas."', '".$notas_guardar."', '".$notas_modificar."', '".$notas_borrar."', '".$lapso."', '".$lismat."', '".$seccion."', '".$tipos_lapso."', '".$nota."', '".$user."', '".$user_clave."', '".$auditoria."', '".$actas."', '".$historiales."', '".$agregar_seccion."', '".$inscribir_materia."', '".$copiar_seccion."', '".$eliminar_seccion."', '".$horas."', '".$aula."');". PHP_EOL);
			}
			$contador++;
		}
	}
	fclose($archivo);
	echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";
}



tabla_electivas("electivas",$fechaActual,$pathname);
function tabla_electivas($tabla,$fechaActual,$pathname)
{
  $archivo = fopen($pathname.chr(92).$fechaActual.chr(92).$tabla.".sql","w");
  fwrite($archivo,"". PHP_EOL);
  fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Respaldo creado el: ".$fechaActual. PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);

    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Estructura de tabla para la tabla `electivas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

    fwrite($archivo,"CREATE TABLE `electivas` (". PHP_EOL);
    fwrite($archivo,"  `id` int(10) NOT NULL AUTO_INCREMENT,". PHP_EOL);
    fwrite($archivo,"  `cod_ele` char(2) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `descrip2` char(100) NOT NULL,". PHP_EOL);
    fwrite($archivo,"  `pensum` char(3) NOT NULL". PHP_EOL);   
    fwrite($archivo,") ENGINE=InnoDB DEFAULT CHARSET=utf8;". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"-- --------------------------------------------------------". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"-- Volcado de datos para la tabla `electivas`". PHP_EOL);
    fwrite($archivo,"--". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);
    fwrite($archivo,"". PHP_EOL);

  fwrite($archivo,"INSERT INTO ".chr(96)."$tabla".chr(96)."(".chr(96)."cod_ele".chr(96).", ".chr(96)."descrip2".chr(96).", ".chr(96)."pensum".chr(96).") VALUES". PHP_EOL);
  include "db.php";
  $sql = "SHOW COLUMNS FROM ".$tabla;
  $contador1=0;
  if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
      $nombre[$contador1]=$fila["Field"]."<br>";
      $contador1++;
    }
  }
  $sql = "SELECT DISTINCT * FROM ".$tabla;
  $resultado = $conn->query($sql);
  $contador=1;
  if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
      $cod_ele = $fila['cod_ele'];
      $descrip2 = $fila['descrip2'];
      $pensum = $fila['pensum'];     
      $cant=$resultado->num_rows;
      if($contador<>$resultado->num_rows){
        fwrite($archivo,"('".$cod_ele."', '".$descrip2."', '".$pensum."'),". PHP_EOL);
      }else{
        fwrite($archivo,"('".$cod_ele."', '".$descrip2."', '".$pensum."');". PHP_EOL);
      }
      $contador++;
    }
  }
  fclose($archivo); 
  echo $pathname.chr(92).$fechaActual.chr(92).$tabla.".sql"."<br>";   
}



?>
