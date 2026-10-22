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

date_default_timezone_set('America/Caracas');

require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


require ('num2letras.php');


date_default_timezone_set('UTC');
$hoy = date("d-m-Y");

$xls = new PHPExcel();

// Se cambio la orientacion de la pagina para mostrar los datos modificados
// modificado : 	08-11-2023
// Ing. Elio A. Milano B.

$xls->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

$xls->createSheet(0);
$xls->setActiveSheetIndex(0);
date_default_timezone_set('America/Caracas');

$img = new PHPExcel_Worksheet_Drawing();

$img->setName('Logo');
$img->setDescription('Logo');
$img->setPath('logoiutpc3.jpg');
$img->setCoordinates('B3');
$img->setHeight(110);
$img->setOffsetX(10);
$img->setOffsetY(-10);
$img->setWorksheet($xls->getActiveSheet());

$Border = array(
	'borders' => array(
		'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '000000'),
			),
		),
	);

// /////////////////////////////////////////////////////////////////

$url=$_POST["url"].".xlsx";	



//  CREA LA ESTRUCTURA EN LA HOJA DE EXCEL PARA SER LLENADOS LUEGO
$i=10;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "LISTADO DE ALUMNOS ACTIVOS");
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->mergeCells('B'.$i.':H'.$i); // COLOCA EL RAYADO DE LA CELDA

$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);//setWidth(5);
$xls->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

$i=13;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "N°");
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C'.$i);
$xls->getActiveSheet()->setCellValue('C'.$i, "CEDULA");
$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "NOMBRE DEL ALUMNO");
$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "CARRERA");
$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "ACTIVIDAD");
$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Muestra el sexo del alumno
$xls->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('G'.$i);
$xls->getActiveSheet()->setCellValue('G'.$i, "SEXO");
$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// muestra el tipo de ingreso - si esta seleccionado por OPSU
$xls->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('H'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('H'.$i);
$xls->getActiveSheet()->setCellValue('H'.$i, "TIPINGRESO");
$xls->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



include('db.php'); 
$sql = "SELECT cedula,nombre,carrera,actividad,sexo,tipingreso FROM alumno WHERE actividad=1 ORDER BY cedula, tipingreso  ASC";// 

$resultado = $conn->query($sql);

$i=14;
$i2=1;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {


switch ($fila["carrera"])  {
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
	case "D":
	$Carrera_a1="P.N.F. DIST. LOGISTICA";
	$Carrera_a2="LOGISTICA";
	break;





}
   /// LLENA LOS DATOS EN EN LAS CELDAS DE EXCEL
		$xls->getActiveSheet()->setCellValue('B'.$i,$i2);
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["cedula"]);		
		$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nombre"]);		
		$xls->getActiveSheet()->setCellValue('E'.$i, $Carrera_a2);		
		$xls->getActiveSheet()->setCellValue('F'.$i, $fila["actividad"]);
		$xls->getActiveSheet()->setCellValue('G'.$i, $fila["sexo"]);
		$xls->getActiveSheet()->setCellValue('H'.$i, $fila["tipingreso"]);

				
        $i++; 
		$i2++;
	}
}else{
	$xls->getActiveSheet()->setCellValue('B'.$i,$LAPSO." carrera");
}
                                 

for ($i3=14; $i3 < $i; $i3++) {              
	$xls->getActiveSheet()->getStyle('B'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('B'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$xls->getActiveSheet()->getStyle('C'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('C'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$xls->getActiveSheet()->getStyle('D'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('D'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$xls->getActiveSheet()->getStyle('E'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('E'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$xls->getActiveSheet()->getStyle('F'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('F'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// ESTABLECE EL BORDE DE LAS CELDAS QUE SE AGREGARON
	// MODIFICA: 08-11-2023
	
	$xls->getActiveSheet()->getStyle('G'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('G'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$xls->getActiveSheet()->getStyle('H'.$i3)->applyFromArray($Border);
     $xls->getActiveSheet()->getStyle('H'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Content-Disposition: attachment;filename='.$url);
header ('Pragma: Excel2007'); 
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$objWriter->save('php://output');
exit;

?>
