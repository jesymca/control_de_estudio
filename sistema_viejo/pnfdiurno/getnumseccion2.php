<?php
$pensum = $_POST['pensum'];
include('/Classes/class_api.php');
$x=new PDF();
echo'<option value="">Seleccionar</option>';
$x->listado_de_secciones($pensum);
?>