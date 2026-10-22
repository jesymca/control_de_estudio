<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['desactivar_alumnos']==1) {
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
	$sql = "SELECT * FROM ".$tabla;
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
	
}	

include('db.php');

$sql = "UPDATE `alumno` SET `actividad`=0";
$conn->query($sql); 

echo "Los alumnos fueron desactivados";


?>