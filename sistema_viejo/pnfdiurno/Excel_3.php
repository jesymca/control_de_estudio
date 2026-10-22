<?php

require_once dirname(__FILE__) . '/PHP_Excel_1.8.0/Classes/PHPExcel.php';

include('db.php'); 

$LAPSO=$_POST["lapso"];
$MATERIA=$_POST["cod_mat"];        
$TIPO =$_POST["tiplap"];
$cod_doc=$_POST["cod_doc"];		
$SECCION=$_POST["seccion"];	
$url=$_POST["url"].".xlsx";	

$LAPSO="2017-2";
$MATERIA="I0AAC";        
$TIPO ="";
$cod_doc=303;		
$SECCION="70";	




$xls = new PHPExcel();

$xls->createSheet(0);
$xls->setActiveSheetIndex(0);
date_default_timezone_set('America/Caracas');


 

$Border = array(
	'borders' => array(
		'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '000000'),
			),
		),
	);


$xls->getActiveSheet()->getColumnDimension('A')->setAutoSize(true); 
$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true); 
$xls->getActiveSheet()->getColumnDimension('C')->setAutoSize(true); 
$xls->getActiveSheet()->getColumnDimension('D')->setAutoSize(true); 
$xls->getActiveSheet()->getColumnDimension('E')->setAutoSize(true); 


$xls->getActiveSheet()->getStyle('A1:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$xls->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A1')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A1');
$xls->getActiveSheet()->setCellValue('A1', "LAPSO: ");

$xls->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A2')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A2');
$xls->getActiveSheet()->setCellValue('A2', "MATERIA: ");

$xls->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A3')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A3');
$xls->getActiveSheet()->setCellValue('A3', "DOCENTE: ");


$xls->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A4')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A4');
$xls->getActiveSheet()->setCellValue('A4', "SECCION: ");

$xls->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A5')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A5');
$xls->getActiveSheet()->setCellValue('A5', "TIPO: ");

/////////////////////////////////////////////////////////////////
$sql = "SELECT * FROM lismat WHERE cod_mat='".$MATERIA."'";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {                      
				$DESCRIP2  = utf8_decode($fila['descrip2']);
				$CRED  = $fila['creditos'];
				$semestre = $fila['semestre'];
				$pensum	= $fila['pensum'];	
				$aprobatori	= $fila['aprobatori'];	
			}  
		}	


$xls->getActiveSheet()->getStyle('B1:B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B1')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B1');
$xls->getActiveSheet()->setCellValue('B1', $LAPSO);

$xls->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B2')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B2');
$xls->getActiveSheet()->setCellValue('B2', $MATERIA);


$xls->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C2')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C2');
$xls->getActiveSheet()->setCellValue('C2', $DESCRIP2);
$xls->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$sql = "SELECT * FROM docente WHERE cod_doc='".$cod_doc."'";
		$resultado = $conn->query($sql);


		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 		
				$CEDULA  = utf8_decode($fila['cedula']);
				$NOMBRE  = utf8_decode($fila['nombre']);
			}
		}

$xls->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B3')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B3');
$xls->getActiveSheet()->setCellValue('B3', $CEDULA);

$xls->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C3')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C3');
$xls->getActiveSheet()->setCellValue('C3', $NOMBRE." (".$cod_doc.")");
$xls->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$xls->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B4')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B4');
$xls->getActiveSheet()->setCellValue('B4', $SECCION);

$xls->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B5')->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B5');
$xls->getActiveSheet()->setCellValue('B5', $TIPO);







$i=7;
$xls->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "N°");

$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "Cedula");

$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C'.$i);
$xls->getActiveSheet()->setCellValue('C'.$i, "Nombre");

$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "Nota");

$xls->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "Observacion");

$xls->getActiveSheet()->getStyle('A7:E7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$xls->getActiveSheet()->getStyle('A7:E7')->getFill()->getStartColor()->setARGB('B5B5B5');


// $xls->getActiveSheet()->getColumnDimension('A')->setWidth(12);

//$xls->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
//$xls->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
//$xls->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
// $xls->getActiveSheet()->getProtection()->setSheet(true);
// $xls->getActiveSheet()->protectCells('A1:D1', 'PHPExcel');
//$xls->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');


// $xls->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
// $xls->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
// $xls->getActiveSheet()->getTabColor()->setARGB('FF0094FF');


include('db.php');    


$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre,lismat.aprobatori FROM notas,alumno,lismat WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$MATERIA."' and notas.lapso='".$LAPSO."' and notas.tiplap='".$TIPO."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and notas.cod_mat=lismat.cod_mat ORDER BY alumno.nombre ASC";
$resultado = $conn->query($sql);

$i=8;
$color_ran=0;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {

        if($color_ran==0){
        $xls->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $xls->getActiveSheet()->getStyle('A'.$i)->getFill()->getStartColor()->setARGB('DDE3C7');
        $xls->getActiveSheet()->getStyle('B'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $xls->getActiveSheet()->getStyle('B'.$i)->getFill()->getStartColor()->setARGB('DDE3C7');
        $xls->getActiveSheet()->getStyle('C'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $xls->getActiveSheet()->getStyle('C'.$i)->getFill()->getStartColor()->setARGB('DDE3C7');
        $xls->getActiveSheet()->getStyle('D'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $xls->getActiveSheet()->getStyle('D'.$i)->getFill()->getStartColor()->setARGB('DDE3C7');
        $xls->getActiveSheet()->getStyle('E'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $xls->getActiveSheet()->getStyle('E'.$i)->getFill()->getStartColor()->setARGB('DDE3C7');
  

          $color_ran=1;
        }else{
          $color_ran=0;
        } 

 

		$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('A'.$i);
		$xls->getActiveSheet()->setCellValue('A'.$i, $i-7);
		$xls->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('B'.$i);
		$xls->getActiveSheet()->setCellValue('B'.$i, $fila["cedula"]);
		$xls->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('C'.$i);	
		$xls->getActiveSheet()->setCellValue('C'.$i, $fila["nombre"]);
		$xls->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('D'.$i);
		$xls->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        //$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nota"]);
		
		if($fila["nota"]<10){
		    $xls->getActiveSheet()->setCellValue('D'.$i, " ".$fila["nota"]);
		} else{
			$xls->getActiveSheet()->setCellValue('D'.$i, $fila["nota"]);
		}
		
		$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //$xls->getActiveSheet()->mergeCells('A1:D1');

        //$xls->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        //$xls->getActiveSheet()->getStyle('A1:D1')->getFill()->getStartColor()->setARGB('FF808080');



		


		$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		if($fila['nota']=="IN"){
			
			$xls->getActiveSheet()->setTitle('E'.$i);
			$xls->getActiveSheet()->setCellValue('E'.$i, "Reprobado");						

		}elseif ($fila['nota']>=$fila['aprobatori']) {

			$xls->getActiveSheet()->setTitle('E'.$i);
			$xls->getActiveSheet()->setCellValue('E'.$i, "Aprobado");
			
		}elseif ($fila['nota']<$fila['aprobatori'] && $fila['nota']<>"IN") {

			$xls->getActiveSheet()->setTitle('E'.$i);
			$xls->getActiveSheet()->setCellValue('E'.$i, "Reprobado");
		}

		$i++;
	}
}




$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', $url));


echo "listo se creo el archivo Excel ".$url;



