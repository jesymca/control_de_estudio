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

$carrera=substr($_POST["pensum"], 0, 1);
$seccion=$_POST["seccion"];
$lapso=$_POST["lapso"];
$url=$_POST["url"].".xlsx";	


include('/Classes/class_api.php');
$con = new PDF();

$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);

$i=10;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "LISTADO DE ALUMNOS DEL ".$carrera_a1." ".$lapso."  SECCION: ".$seccion);
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->mergeCells('B'.$i.':I'.$i);


$xls->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);//setWidth(5);
$xls->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);



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
$xls->getActiveSheet()->setCellValue('E'.$i, "TELEFONO 1");
$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "CELULAR");
$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('G'.$i);
$xls->getActiveSheet()->setCellValue('G'.$i, "TELEFONO 3");
$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('H'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('H'.$i);
$xls->getActiveSheet()->setCellValue('H'.$i, "TELEFONO 4");
$xls->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('I'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('I'.$i);
$xls->getActiveSheet()->setCellValue('I'.$i, "DIRECCION");
$xls->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



include('db.php'); 
                                                                                                            
$sql = "SELECT DISTINCT alumno.cedula,alumno.nombre,alumno.direccion,alumno.telefonoh,alumno.telefonoc,alumno.telefonot,alumno.email,alumno.actividad,notas.seccion FROM notas,alumno WHERE notas.codigo=alumno.cedula and notas.carrera='".$carrera."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' ORDER BY alumno.nombre ASC";
$resultado = $conn->query($sql);

$i=14;
$i2=1;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$xls->getActiveSheet()->setCellValue('B'.$i,$i2);
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["cedula"]);		
		$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nombre"]);		
		$xls->getActiveSheet()->setCellValue('E'.$i, $fila["telefonoh"]);
		$xls->getActiveSheet()->setCellValue('F'.$i, $fila["telefonoc"]);
		$xls->getActiveSheet()->setCellValue('G'.$i, $fila["telefonot"]);
		$xls->getActiveSheet()->setCellValue('H'.$i, $fila["email"]);
	    $xls->getActiveSheet()->setCellValue('I'.$i, $fila["direccion"]);
       

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
    $xls->getActiveSheet()->getStyle('G'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('G'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $xls->getActiveSheet()->getStyle('H'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('H'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $xls->getActiveSheet()->getStyle('I'.$i3)->applyFromArray($Border);
    $xls->getActiveSheet()->getStyle('I'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    

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
