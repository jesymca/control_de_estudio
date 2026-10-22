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
$cod_doc=$_POST["cod_doc1"];		
$SECCION=$_POST["seccion1"];	
$url=$_POST["url"].".xlsx";	


if(substr($MATERIA, 1, 1)=="P" OR substr($MATERIA, 1, 1)=="T" OR substr($MATERIA, 1, 1)=="E"){
	$TIPO=substr($MATERIA, 1, 1);
}else{
	$TIPO="";
}


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
$img->setCoordinates('A3');
$img->setHeight(110);
$img->setOffsetX(10);
$img->setOffsetY(-10);
$img->setWorksheet($xls->getActiveSheet());


$i=2;
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setSize(16);
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "REPÚBLICA BOLIVARIANA DE VENEZUELA");
$i=3;

$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "MINISTERIO DEL PODER POPULAR PARA EDUCACIÓN UNIVERSITARIA");
$i=4;

$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "CIENCIA Y TECNOLOGÍA");

$i=5;
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "UNIVERSIDAD POLITÉCNICA TERRITORIAL");

$i=6;
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "PUERTO CABELLO");

$i=7;
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "DEPARTAMENTO DE CONTROL DE ESTUDIO");


$i=10;
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setSize(14);
$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "ACTA DE CALIFICACION FINAL ".$LAPSO." ".$TIPO);
$xls->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);


$xls->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$xls->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$xls->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$xls->getActiveSheet()->getColumnDimension('D')->setWidth(50); 
$xls->getActiveSheet()->getColumnDimension('E')->setWidth(7);
$xls->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$xls->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

include('db.php'); 
$sql = "SELECT * FROM lismat WHERE cod_mat='".$MATERIA."'";
		$resultado = $conn->query($sql);

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {                      
				//$DESCRIP2  = utf8_decode($fila['descrip2']);
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


include('/Classes/class_api.php');
$con = new PDF();
$carrera_a1 = $con->carrera_larga($carrera);
$carrera_a2 = $con->carrera_corta($carrera);


$i=12;
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "ASIGNATURA:     ".$DESCRIP2." (".$MATERIA.")");

$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "SECCIÓN: ".$semestre." - ".$SECCION."                  U.C.: ".$CRED);


$i=14;

$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "DOCENTE:     ".$NOMBRE." (".$cod_doc.")                                                                           CÉDULA:  ".$CEDULA);

$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "DEPT.:   ".$carrera_a2);


$i=16;
$xls->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "NUM.");

$xls->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('B'.$i);
$xls->getActiveSheet()->setCellValue('B'.$i, "CODIGO");


$xls->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('C'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('C'.$i);
$xls->getActiveSheet()->setCellValue('C'.$i, "CEDULA");

$xls->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('D'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i, "NOMBRE DEL ALUMNO");

$xls->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('E'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i, "NOTA");

$xls->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "LETRA");


$xls->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
$xls->getActiveSheet()->setTitle('G'.$i);
$xls->getActiveSheet()->setCellValue('G'.$i, "Observacion");

$xls->getActiveSheet()->getStyle('A16:G16')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$xls->getActiveSheet()->getStyle('A16:G16')->getFill()->getStartColor()->setARGB('B5B5B5');


include('db.php');    


$sql = "SELECT DISTINCT notas.id,notas.acu,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre,lismat.aprobatori FROM notas,alumno,lismat WHERE notas.seccion='".$SECCION."' and notas.cod_mat='".$MATERIA."' and notas.lapso='".$LAPSO."' and notas.tiplap='".$TIPO."' and notas.cod_doc='".$cod_doc."' and alumno.cedula=notas.codigo and notas.cod_mat=lismat.cod_mat ORDER BY alumno.nombre ASC";
$resultado = $conn->query($sql);

$i=17;
$color_ran=0;
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) {


		$xls->getActiveSheet()->getStyle('A'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('A'.$i);
		$xls->getActiveSheet()->setCellValue('A'.$i, $i-16);
		$xls->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$xls->getActiveSheet()->getStyle('B'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('B'.$i);
		$xls->getActiveSheet()->setCellValue('B'.$i, $fila["id"]);
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
		$xls->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    
		if($fila["nota"]<10){
		    $xls->getActiveSheet()->setCellValue('E'.$i, " ".$fila["nota"]);
		} else{
			$xls->getActiveSheet()->setCellValue('E'.$i, $fila["nota"]);
		}
		
		$xls->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



		$xls->getActiveSheet()->getStyle('F'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->setTitle('F'.$i);	
		if($fila['nota']<10 AND $fila['nota']<>"IN"){
			  $xls->getActiveSheet()->setCellValue('F'.$i, "CERO ".strtoupper(num2letras($fila['nota']))); 
            }else{
              $xls->getActiveSheet()->setCellValue('F'.$i, strtoupper(num2letras($fila['nota'])));              
            } 
		$xls->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



		$xls->getActiveSheet()->getStyle('G'.$i)->applyFromArray($Border);
		$xls->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		if($fila['nota']=="IN"){
			
			$xls->getActiveSheet()->setTitle('G'.$i);
			$xls->getActiveSheet()->setCellValue('G'.$i, "Reprobado");					
            $IN=$IN+1;
		}elseif ($fila['nota']>=$fila['aprobatori']) {

			$xls->getActiveSheet()->setTitle('G'.$i);
			$xls->getActiveSheet()->setCellValue('G'.$i, "Aprobado");      
			$APRO=$APRO+1;
		}elseif ($fila['nota']<$fila['aprobatori'] && $fila['nota']<>"IN") {

			$xls->getActiveSheet()->setTitle('G'.$i);
			$xls->getActiveSheet()->setCellValue('G'.$i, "Reprobado");
		    $REPR=$REPR+1;
		}

		$i++;
	}
}




 $xls->getActiveSheet()->setTitle('A'.$i);
 $xls->getActiveSheet()->setCellValue('A'.$i,"****************************    FIN DEL ACTA    ****************************");
 $xls->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 $xls->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
 $xls->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($Border);





$i++;
$e=$i+3;

$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "Conforme");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$xls->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($Border);

$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "Fecha:              ".$hoy);
$xls->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
$xls->getActiveSheet()->getStyle('F'.$i.':G'.$i)->applyFromArray($Border);



$xls->getActiveSheet()->getStyle('D'.$i.':E'.$e)->applyFromArray($Border);

$i++;
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "Prof. Materia");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$xls->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($Border);


$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "Aprobados:             ".$APRO);
$xls->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
$xls->getActiveSheet()->getStyle('F'.$i.':G'.$i)->applyFromArray($Border);


$i++;
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "Control Estudio");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$xls->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($Border);

$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "Reprobados            ".$REPR);
$xls->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
$xls->getActiveSheet()->getStyle('F'.$i.':G'.$i)->applyFromArray($Border);


$i++;
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i, "Directo");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$xls->getActiveSheet()->getStyle('A'.$i.':C'.$i)->applyFromArray($Border);

$xls->getActiveSheet()->setTitle('F'.$i);
$xls->getActiveSheet()->setCellValue('F'.$i, "Inasistentes:            ".$IN);
$xls->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
$xls->getActiveSheet()->getStyle('F'.$i.':G'.$i)->applyFromArray($Border);


$i++;
$xls->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i,"ORIGINAL Y COPIA:");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);


$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i,"Departamento de Control de Estudios");
$i++;
$xls->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$xls->getActiveSheet()->setTitle('A'.$i);
$xls->getActiveSheet()->setCellValue('A'.$i,"TRIPLICADO:");
$xls->getActiveSheet()->mergeCells('A'.$i.':C'.$i);

$sql = "SELECT * FROM user WHERE login='".$_SESSION['username']."'";
		$resultado = $conn->query($sql);
		
		$CREDSUM=0;
		
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) { 
				$NOMBRE_USER=$fila['nombre'];              
			}
		}

$xls->getActiveSheet()->setTitle('D'.$i);
$xls->getActiveSheet()->setCellValue('D'.$i,"Para ser Publicado");

$xls->getActiveSheet()->setTitle('E'.$i);
$xls->getActiveSheet()->setCellValue('E'.$i,"USUARIO:  ".$NOMBRE_USER);



$d=$i-5;

$xls->getActiveSheet()->setTitle('D'.$d);
$xls->getActiveSheet()->setCellValue('D'.$d, "Observacione");


$d++;
$xls->getActiveSheet()->setTitle('D'.$d);
$xls->getActiveSheet()->setCellValue('D'.$d, $MATERIA." - ".$SECCION);



$d++;
$xls->getActiveSheet()->setTitle('D'.$d);
$xls->getActiveSheet()->setCellValue('D'.$d, $carrera_a2);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Content-Disposition: attachment;filename='.$url);
header ('Pragma: Excel2007'); 
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$objWriter->save('php://output');
exit;
