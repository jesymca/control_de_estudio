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


$LAPSO=$_POST["lapso"];
$url=$_POST["url"].".xlsx";	
$LAPSO="2020-1";

include('/Classes/class_api.php');
$con=new PDF();

$pensum = $con->pensum($carrera);
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);

$i=10;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "LISTADO DE ALUMNOS A COMEDOR LAPSO ".$LAPSO);
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);//setWidth(5);
$xls->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);


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
$xls->getActiveSheet()->setCellValue('D'.$i, "NOMBRE");
$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "CARRERA");
$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

include('db.php'); 
$LAPSO="2020-1";
$sql = "SELECT DISTINCT SUBSTRING(alumno.cedula,2,9) as CEDULA,alumno.nombre as NOMBRE,pensum.descripcion2 as CARRERA FROM `alumno`,`pensum`,`notas` WHERE alumno.carrera=SUBSTRING(pensum.pensum,1,1) and notas.codigo=alumno.cedula and notas.lapso='".$LAPSO."' ORDER BY alumno.nombre ASC"; 
$resultado = $conn->query($sql);

$i=14;
$i2=1;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$xls->getActiveSheet()->setCellValue('B'.$i,$i2);
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["CEDULA"]);		
		$xls->getActiveSheet()->setCellValue('D'.$i, strtoupper($fila["NOMBRE"]));		
		$xls->getActiveSheet()->setCellValue('E'.$i, $fila["CARRERA"]);					
        $i++; 
		$i2++;
	}
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
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Content-Disposition: attachment;filename='.$url);
header ('Pragma: Excel2003'); 
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2003');
$objWriter->save('php://output');
exit;

?>
