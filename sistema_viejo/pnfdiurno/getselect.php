<?php

$opcion=$_POST["opcion"];
$pensum=$_POST["pensum"];
$cod_mat=$_POST["cod_mat"];
$cod_doc=$_POST["cod_doc"];
$lapso=$_POST["lapso"];
$seccion=$_POST["seccion"];
$campos=$_POST["campos"];
$cedula=$_POST["cedula"];
$grado=$_POST["grado"];

include('/Classes/class_api.php');
$pdf=new PDF();

switch ($opcion) {
	
	case 'pensum':
	$pdf->getpensum();
	break;

	case 'getmateria':
	$pdf->getmateria($pensum);
	break;

	case 'getmateria_no_vista':
	$pdf->getmateria_no_vista($pensum,$cedula,$grado);
	break;

	case 'getmateria2':
	$pdf->getmateria2($pensum,$cod_doc,$campos);
	break;

	case 'getmateria3':
	$pdf->getmateria3($pensum,$cedula);
	break;

	case 'getelectivas':
	$pdf->getelectivas($pensum,$cod_mat);
	break;

	case 'getelectivas2':
	$pdf->getelectivas($pensum,$cod_mat,$campos);
	break;

	case 'getnumseccion':
	$pdf->getnumseccion($pensum);
	break;

	case 'getnumseccion2':
	$pdf->getnumseccion2($cod_doc,$cod_mat,$lapso);
	break;	

	case 'getnumseccion3':
	$pdf->getnumseccion3($cod_doc,$cod_mat,$lapso,$campos);
	break;	

	case 'getdocente':
	$pdf->getdocente($pensum,$campos);
	break;	

    case 'getlapso':
	$pdf->getlapso($pensum,$cod_doc,$cod_mat,$campos);
	break;

	case 'getcantidad':
	$pdf->getcantidad($cod_doc,$cod_mat,$lapso,$seccion);
	break;	
    
    case 'getcantidad2':
	$pdf->getcantidad2($pensum,$lapso,$seccion);
	break;

    case 'getgrado':
	$pdf->getgrado($pensum);
	break;	
	

}

?>