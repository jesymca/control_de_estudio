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


$carrera=substr($_POST["carrera1"], 0, 1);
$SECCION=$_POST["seccion1"];
$LAPSO=$_POST["lapso1"];
$url=$_POST["url"].".xlsx";	

include('/Classes/class_api.php');
$con = new PDF();

$pensum = $con->pensum($carrera);
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);

$i=10;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "LISTADO DE ALUMNOS DEL ".$carrera_a1." ".$LAPSO);
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
$xls->getActiveSheet()->setCellValue('F'.$i, "SECCION");
$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


include('db.php'); 

$sql = "SELECT DISTINCT alumno.cedula,alumno.nombre,notas.seccion,alumno.actividad FROM notas,alumno WHERE notas.codigo=alumno.cedula and notas.carrera='".$carrera."' and notas.seccion='".$SECCION."' and notas.lapso='".$LAPSO."' and actividad=1 ORDER BY alumno.nombre ASC";// 
$resultado = $conn->query($sql);

$i=14;
$i2=1;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$xls->getActiveSheet()->setCellValue('B'.$i,$i2);
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["cedula"]);		
		$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nombre"]);		
		$xls->getActiveSheet()->setCellValue('E'.$i, $carrera_a2);		
		$xls->getActiveSheet()->setCellValue('F'.$i, $fila["seccion"]);
				
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
	$xls->getActiveSheet()->getStyle('F'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('F'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
