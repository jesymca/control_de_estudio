<?php

// include('HISTORI.php');
// $x=new PDF();

include('db.php');
$pensum=$_POST["pensum"];

// if($docente==""){
//  $docente=0;
// }

// $tra_x[0]=$x->verficar_trayecto($cedula,$pensum,0,"T");
// $tra_x[1]=$x->verficar_trayecto($cedula,$pensum,1,"T");
// $tra_x[2]=$x->verficar_trayecto($cedula,$pensum,2,"T"); 
// if($tra_x[0]=="true" and $tra_x[1]=="true" and $tra_x[2]=="true"){
//   if($pensum=="GXC"){
//     $tra_x[3]=$x->verficar_trayecto($cedula,$pensum,3,"L");    
//     $tra_x[4]=$x->verficar_trayecto($cedula,$pensum,4,"L");
//   }else{
//    $tra_x[3]=$x->verficar_trayecto($cedula,$pensum,3,"I");    
//    $tra_x[4]=$x->verficar_trayecto($cedula,$pensum,4,"I");  
//  } 
// }

// $lapso_actual=$x->lapso_actual(substr($fila["cod_mat"], 0, 1));
$sql = "SELECT DISTINCT lismat.descrip2,lismat.cod_mat,lismat.semestre,lismat.grado,lismat.trayecto FROM lismat where lismat.pensum='".$pensum."' ORDER BY lismat.trayecto,lismat.semestre ASC";
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {
  echo '<option value="">Seleccionar</option>';
  while($fila = $resultado->fetch_assoc()) {
   // $materia=$x->listado_materia($pensum,substr($fila["cod_mat"], 2, 2));
   // list($cod_mat,$descrip2,$creditos,$semestre,$nota_cat,$divicion,$trayecto,$aprobatori,$electiva) = split('[|]', $materia);
   // $resumida= $x->calcular_resumida($pensum,$X1B,$cod_mat,$cedula,$X1B,$descrip2,$semestre,$creditos,$trayecto,$aprobatori,$divicion,$electiva,"false")."<br>";
   // list($nota_resumida, $lapso_resumida,$tipo_resumida) = split('[|]', $resumida);
   
   // if ($aprobatori>$nota_resumida and $x->lapso_actual(substr($fila["cod_mat"], 0, 1))<>$lapso_resumida and $tra_x[$trayecto]=="false") {
    echo '<option value="'.$fila["cod_mat"].'">'.$fila["cod_mat"].' - '.$fila["trayecto"].' - '.$fila["semestre"].' - '.$fila["grado"].' - '.$fila["descrip2"].'</option>';
  //} 
}
}

?>