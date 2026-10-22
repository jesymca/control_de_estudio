<?php
include('/Classes/class_api.php');
$pdf=new PDF();

$txt="Todos necesitamos disciplina como demostración y prueba de nuestra filiación La disciplina de la mano de Dios nos demuestra su amor y nos enseña que somos sus hijos. El Padre celestial es el patrón para la paternidad terrenal. Un padre terrenal bueno establecerá una firme disciplina: bien, sanidad, amor, corrección, enseñanza e instrucciones, disciplina madura en el hogar. La liberación nunca toma el sitio de la disciplina, sólo la complementa. Uno debe soportar la disciplina. La voluntad y la decisión de mantener y tolerar la corrección se deben entretejer en el carácter y la personalidad del niño. En muchas situaciones debemos estimular y alentar a nuestros hijos: Muy bien, aprendamos a soportar; no nos escondamos; no vamos a renunciar ni a ceder; dejemos de retirarnos y de pensar siquiera en huir. A fin de tener una familia que pueda vencer la adversidad, los padres deben enseñar y dar ejemplo de la capacidad para soportar. Hay muchísimas oportunidades para probar esa capacidad, p.e.: padres poco funcionales, familias inestables, rivalidades entre los miembros del hogar y de la familia, conflictos de personalidad, violación de la intimidad y de las responsabilidades que irritan y molestan la carne.";



$pdf = new FPDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','B',16);

$pdf->SetFont('Arial','',16);
$pdf->MultiCell(0,5,$txt,0,'J',false);//utf8_decode($txt)
$pdf->Output();

?>
