<?php

include('db.php');

$pensum=$_POST["pensum"];
$cedula=$_POST["cedula"];
$cod_doc=0;

echo '<option value="">Seleccionar</option>';

include('HISTORI.php');
$x=new PDF();
$x->verficar_trayecto_select($cedula,$pensum,0,"T");
$x->verficar_trayecto_select($cedula,$pensum,1,"T");
$x->verficar_trayecto_select($cedula,$pensum,2,"T"); 

  if($pensum=="GXC" OR $pensum=="CXC"){
     $x->verficar_trayecto_select($cedula,$pensum,3,"L");    
     $x->verficar_trayecto_select($cedula,$pensum,4,"L");
   }else{
    $x->verficar_trayecto_select($cedula,$pensum,3,"I");    
    $x->verficar_trayecto_select($cedula,$pensum,4,"I");  
  } 

?>