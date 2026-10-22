<?php

// session_start();
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
// } else {
// 	header("Location: index.html");
// 	exit;
// }
// $now = time();
// if($now > $_SESSION['expire']) {
// 	session_destroy();
// 	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
// 	exit;
// }

date_default_timezone_set('America/Caracas');

require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

include('db.php'); 
require ('num2letras.php');

define('ruta_de_copiado', "D:".chr(92)."estadistica_y_nominas_2019".chr(92));

date_default_timezone_set('UTC');
$hoy = date("d-m-Y");

$LAPSO=$_POST["lapso"];
$MATERIA=$_POST["cod_mat"];        
$TIPO =$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];		
$SECCION=$_POST["seccion"];

$url=$cod_doc."-".$SECCION.".xlsx";	
$url1=$cod_doc."-".$SECCION;	


$carrera=substr($MATERIA, 0, 1);


include('getpensum3_clase.php');
$con = new carreras();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);


include "db.php";
$sql = "UPDATE `alumno`,`notas` SET `alumno`.actividad=1 WHERE`notas`.`cod_mat`='".$MATERIA."' AND `notas`.`lapso`='".$LAPSO."' AND `notas`.`seccion`='".$SECCION."' AND `notas`.cod_doc='".$cod_doc."' AND `alumno`.cedula=`notas`.codigo";
$resultado = $conn->query($sql);



$archivo = fopen(ruta_de_copiado.chr(92).$carrera_a2."-".$url1.".sql","w");

fwrite($archivo,"". PHP_EOL);


fwrite($archivo,"SELECT `alumno`.`cedula`,`alumno`.`nombre`,`alumno`.`carrera` FROM `alumno`,`notas` WHERE `notas`.`cod_mat`=".chr(34).$_POST["cod_mat"].chr(34)." AND `notas`.`lapso`=".chr(34).$_POST["lapso"].chr(34)." AND `notas`.`seccion`=".chr(34).$_POST["seccion"].chr(34)." AND `notas`.`cod_doc`=".chr(34).$_POST["cod_doc"].chr(34)." AND `alumno`.`cedula`=`notas`.`codigo`". PHP_EOL);

fwrite($archivo,"". PHP_EOL);
fwrite($archivo,"". PHP_EOL);

fwrite($archivo,"UPDATE `alumno`,`notas` SET `alumno`.actividad=1 WHERE `notas`.`cod_mat`=".chr(34).$_POST["cod_mat"].chr(34)." AND `notas`.`lapso`=".chr(34).$_POST["lapso"].chr(34)." AND `notas`.`seccion`=".chr(34).$_POST["seccion"].chr(34)." AND `notas`.cod_doc=".chr(34).$_POST["cod_doc"].chr(34)." AND `alumno`.cedula=`notas`.codigo". PHP_EOL);
fwrite($archivo,"". PHP_EOL);
fclose($archivo);



$xls = new PHPExcel();

$Border = array(
	'borders' => array(
		'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '000000'),
			),
		),
	);

$xls->createSheet(0);
$xls->setActiveSheetIndex(0);
date_default_timezone_set('America/Caracas');

$img = new PHPExcel_Worksheet_Drawing();
$img->setName('Logo');
$img->setDescription('Logo');
$img->setPath('LOGO.jpg');
$img->setCoordinates('B3');
$img->setHeight(130);
$img->setOffsetX(10);
$img->setOffsetY(-10);
$img->setWorksheet($xls->getActiveSheet());



$i=10;
$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('C'.$i);
$xls->getActiveSheet()->setCellValue('C'.$i, "NOMINA DE ASISTENACIA ESTUDIANTIL ".$LAPSO." ".$TIPO);
$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$xls->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


// /////////////////////////////////////////////////////////////////
include('db.php'); 

$sql = "SELECT * FROM lismat WHERE cod_mat='".$MATERIA."'";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {            
		
		$DESCRIP2  = $fila['descrip2'];
		$CRED  = $fila['creditos'];
		$semestre = $fila['semestre'];
		$pensum	= $fila['pensum'];	
		$aprobatori	= $fila['aprobatori'];	
	}  
}	


$sql = "SELECT * FROM docente WHERE cod_doc='".$cod_doc."'";
$resultado = $conn->query($sql);


if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 		
		$CEDULA  = utf8_decode($fila['cedula']);
		$NOMBRE  = utf8_decode($fila['nombre']);
	}
}

$carrera=substr($MATERIA, 0, 1);


// switch ($carrera)  {
// 	case "M":
// 	$carrera_a1="P.N.F. MECANICA";
// 	$carrera_a2="MECANICA";
// 	break;
// 	case "T":
// 	$carrera_a1="P.N.F. MANTENIMIENTO";
// 	$carrera_a2="MANTENIMIENTO";
// 	break;
// 	case "E":
// 	$carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
// 	$carrera_a2="MATERIALES";
// 	break;
// 	case "I":
// 	$carrera_a1="P.N.F. INFORMATICA";
// 	$carrera_a2="INFORMATICA";
// 	break;
// 	case "G":
// 	$carrera_a1="P.N.F. TURISMO";
// 	$carrera_a2="TURISMO";
// 	break;   
// 	case "O":
// 	$carrera_a1="P.I.F. MECANICA TERMICA";
// 	$carrera_a2="TERMICA";
// 	break;
// 	case "A":
// 	$carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
// 	$carrera_a2="AUTOMOTRIZ";
// 	break;
// 	case "C":
// 	$carrera_a1="P.N.F. CIENCIA FISCALES";
// 	$carrera_a2="CIENCIA FISCALES";
// 	break;
// }


$i=12;
$xls->getActiveSheet()->setTitle('A'.$i);                       
$xls->getActiveSheet()->setCellValue('A'.$i, "ASIGNATURA:     ".substr($DESCRIP2, 0, 19)." (".$MATERIA.")"."                                            SECCIÓN: ".$semestre." - ".$SECCION."    AULA:                                                  FECHA:  ".$hoy);



$i=14;

$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "DOCENTE:     ".$NOMBRE." (".$cod_doc.")                                                                                CÉDULA:  ".$CEDULA."                 FIRMA.:   ");




$i=17;
$xls->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "#");
$xls->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "CEDULA");
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C'.$i);
$xls->getActiveSheet()->setCellValue('C'.$i, "NOMBRE DEL ALUMNO");
$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "CARRERA");
$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "ACTIVIDAD");
$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



include('db.php');    


$sql = "SELECT `alumno`.`cedula`,`alumno`.`nombre`,`alumno`.`carrera`,`alumno`.`actividad` FROM `alumno`,`notas` WHERE `notas`.`cod_mat`=".chr(34).$MATERIA.chr(34)." AND `notas`.`lapso`=".chr(34).$LAPSO.chr(34)." AND `notas`.`seccion`=".chr(34).$SECCION.chr(34)." AND `notas`.`cod_doc`=".chr(34).$cod_doc.chr(34)." AND `alumno`.`cedula`=`notas`.`codigo` ORDER BY alumno.nombre ASC";
$resultado = $conn->query($sql);

$i=18;
$color_ran=0;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('A'.$i);
		$xls->getActiveSheet()->setCellValue('A'.$i, $i-17);
		$xls->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('B'.$i);
		$xls->getActiveSheet()->setCellValue('B'.$i, $fila["cedula"]);
		$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('C'.$i);	
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["nombre"]);
		$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


// switch ($fila["carrera"])  {
// 	case "M":
// 	$carrera_a1="P.N.F. MECANICA";
// 	$carrera_a2="MECANICA";
// 	break;
// 	case "T":
// 	$carrera_a1="P.N.F. MANTENIMIENTO";
// 	$carrera_a2="MANTENIMIENTO";
// 	break;
// 	case "E":
// 	$carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
// 	$carrera_a2="MATERIALES";
// 	break;
// 	case "I":
// 	$carrera_a1="P.N.F. INFORMATICA";
// 	$carrera_a2="INFORMATICA";
// 	break;
// 	case "G":
// 	$carrera_a1="P.N.F. TURISMO";
// 	$carrera_a2="TURISMO";
// 	break;   
// 	case "O":
// 	$carrera_a1="P.I.F. MECANICA TERMICA";
// 	$carrera_a2="TERMICA";
// 	break;
// 	case "A":
// 	$carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
// 	$carrera_a2="AUTOMOTRIZ";
// 	break;
// 	case "C":
// 	$carrera_a1="P.N.F. CIENCIA FISCALES";
// 	$carrera_a2="CIENCIA FISCALES";
// 	break;
// }

		$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('D'.$i);	
		$xls->getActiveSheet()->setCellValue('D'.$i,$carrera_a2);
		$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
        $xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('E'.$i);	
		$xls->getActiveSheet()->setCellValue('E'.$i,$fila["actividad"]);
		$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$i++;
	}
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Content-Disposition: attachment;filename='.$url);
header ('Pragma: Excel2007'); 
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$objWriter->save('php://output');


exit;
