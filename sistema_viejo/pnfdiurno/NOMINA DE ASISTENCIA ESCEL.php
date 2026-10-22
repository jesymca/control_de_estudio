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

$LAPSO=$_POST["lapso1"];
$MATERIA=$_POST["cod_mat1"];        
$TIPO =$_POST["tiplap1"];
$cod_doc=$_POST["cod_doc1"];		
$SECCION=$_POST["seccion1"];	
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
$img->setPath('LOGO.jpg');
$img->setCoordinates('B3');
$img->setHeight(130);
$img->setOffsetX(10);
$img->setOffsetY(-10);
$img->setWorksheet($xls->getActiveSheet());


// $i=2;
// $xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(16);
// $xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "REPÚBLICA BOLIVARIANA DE VENEZUELA");


// $i=3;

// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA");

// $i=4;

// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "CIENCIA Y TECNOLOGÍA");



// $i=5;
// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "INSTITUTO UNIVERSITARIO DE TECNOLOGIA");


// $i=6;
// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "PUERTO CABELLO");


// $i=7;
// $xls->getActiveSheet()->setTitle('C'.$i);
// $xls->getActiveSheet()->setCellValue('C'.$i, "DEPARTAMENTO DE CONTROL DE ESTUDIO");


$i=10;
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "NOMINA DE ASISTENACIA ESTUDIANTIL ".$LAPSO." ".$TIPO);
$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('C')->setWidth(50);
$xls->getActiveSheet()->getColumnDimension('D')->setWidth(50); 


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



$xls->getActiveSheet()->getStyle('A16:C16')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A16:C16')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A16');
$xls->getActiveSheet()->setCellValue('A16', "DATOS DEL ALUMNO");
$xls->getActiveSheet()->mergeCells('A16:C16');
$xls->getActiveSheet()->getStyle('A16:C16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$xls->getActiveSheet()->getStyle('D16:G16')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('D16:G16')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D16');
$xls->getActiveSheet()->setCellValue('D16', "NOTAS ADQUIRIDAS");
$xls->getActiveSheet()->mergeCells('D16:G16');
$xls->getActiveSheet()->getStyle('D16:G16')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->getStyle('D16:G16')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$xls->getActiveSheet()->getColumnDimension('D')->setWidth(6);
$xls->getActiveSheet()->getStyle('D17')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D17');
$xls->getActiveSheet()->setCellValue('D17', "1");
$xls->getActiveSheet()->getStyle('D17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
$xls->getActiveSheet()->getColumnDimension('E')->setWidth(6);
$xls->getActiveSheet()->getStyle('E17')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E17');
$xls->getActiveSheet()->setCellValue('E17', "2");
$xls->getActiveSheet()->getStyle('E17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
$xls->getActiveSheet()->getColumnDimension('F')->setWidth(6);
$xls->getActiveSheet()->getStyle('F17')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('F17');
$xls->getActiveSheet()->setCellValue('F17', "3");
$xls->getActiveSheet()->getStyle('F17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getColumnDimension('G')->setWidth(6);
$xls->getActiveSheet()->getStyle('G17')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('G17');
$xls->getActiveSheet()->setCellValue('G17', "T");
$xls->getActiveSheet()->getStyle('G17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 

$xls->getActiveSheet()->getStyle('H16:J17')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('H16:J17')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('H16');
$xls->getActiveSheet()->setCellValue('H16', "OBSERVACION");
$xls->getActiveSheet()->mergeCells('H16:J17');
$xls->getActiveSheet()->getStyle('H16:J17')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->getStyle('H16:J17')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



include('db.php');    


$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre,lismat.aprobatori FROM notas,alumno,lismat WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$MATERIA."' and notas.lapso='".$LAPSO."' and notas.tiplap='".$TIPO."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and notas.cod_mat=lismat.cod_mat ORDER BY alumno.nombre ASC";
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

	    $xls->getActiveSheet()->getColumnDimension('D')->setWidth(6);
	    $xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('D'.$i);
		$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
	    $xls->getActiveSheet()->getColumnDimension('E')->setWidth(6);
	    $xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('E'.$i);
		$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	    $xls->getActiveSheet()->getColumnDimension('F')->setWidth(6);
	    $xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('F'.$i);
		$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	    $xls->getActiveSheet()->getColumnDimension('G')->setWidth(6);
	    $xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('G'.$i);
		$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

  	    
        $xls->getActiveSheet()->getStyle('H'.$i.":J".$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('H'.$i);
		$xls->getActiveSheet()->mergeCells('H'.$i.":J".$i);
		$xls->getActiveSheet()->getStyle('H'.$i.":J".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 	   

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
