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

include('db.php'); 
require ('num2letras.php');


date_default_timezone_set('UTC');
$hoy = date("d-m-Y");

$LAPSO=$_POST["lapso"];
$MATERIA=$_POST["cod_mat"];        
$TIPO =$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];		
$SECCION=$_POST["seccion"];	
$url=$_POST["url"].".xlsx";	


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
$img->setPath('logoiutpc3.jpg');
$img->setCoordinates('A3');
$img->setHeight(130);
$img->setOffsetX(10);
$img->setOffsetY(-10);
$img->setWorksheet($xls->getActiveSheet());



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


        include('getpensum3_clase.php');
        $con = new carreras();
        $carrera_a1 = $con->carrera_larga($carrera);
        $carrera_a2 = $con->carrera_corta($carrera);


$i=10;
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "LISTADO DE ALUMNO ".$LAPSO." ".$TIPO."  ".$SECCION."  ".$carrera_a2);
$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->mergeCells('B'.$i.':G'.$i);

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

$xls->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('G'.$i);
$xls->getActiveSheet()->setCellValue('G'.$i, "LAPSO");
$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


include('db.php');    


$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre,lismat.aprobatori FROM notas,alumno,lismat WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$MATERIA."' and notas.lapso='".$LAPSO."' and notas.tiplap='".$TIPO."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and notas.cod_mat=lismat.cod_mat ORDER BY alumno.nombre ASC";
$resultado = $conn->query($sql);

$i=14;
$color_ran=0;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

		$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('B'.$i);
		$xls->getActiveSheet()->setCellValue('B'.$i, $i-13);
		$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('C'.$i);
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["cedula"]);
		$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('D'.$i);	
		$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nombre"]);
		$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('E'.$i);	
		$xls->getActiveSheet()->setCellValue('E'.$i, $carrera_a2);
		$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('F'.$i);	
		$xls->getActiveSheet()->setCellValue('F'.$i, $SECCION);
		$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('G'.$i);	
		$xls->getActiveSheet()->setCellValue('G'.$i, $LAPSO);
		$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);




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
