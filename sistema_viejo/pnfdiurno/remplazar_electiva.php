
<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>

      <th width="1%">Codigo</th>
      <th width="50%">Nombre</th>
      <th width="1%">Materia</th>
      <th width="1%">Electiva</th> 
      <th width="1%">Cod_doc</th>                                     
      <th width="10%">Lapso</th>                
      <th width="1%">Seccion</th> 
 
      
    </tr>
  </thead>              
</table>

<?php
session_start();                                
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['cambiar_docente']==1) {
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
$accion=$_POST['accion'];

$cod_mat = $_POST['cod_mat'];
$electiva = $_POST['electiva'];
$cod_doc = $_POST['cod_doc'];           
$lapso = $_POST['lapso'];                 
$seccion = $_POST['seccion'];





require("db.php");

if($accion=="buscar") {                                                                                                                                     
  $sql = "SELECT notas.codigo,notas.cod_mat,notas.cod_doc,notas.lapso,notas.seccion,notas.electiva,alumno.nombre FROM notas,alumno where notas.cod_mat='".$cod_mat."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' and notas.cod_doc='".$cod_doc."' and notas.codigo=alumno.cedula";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      echo '<tr>                              
      <td>'.$row["codigo"].'</td>       
      <td>'.$row["nombre"].'</td>                            
      <td>'.$row["cod_mat"].'</td>
      <td>'.$row["electiva"].'</td>                 
      <td>'.$row["cod_doc"].'</td>                               
      <td>'.$row["lapso"].'</td>                   
      <td>'.$row["seccion"].'</td>          
    </tr>';

  }
} 

}elseif ($accion=="actualizar") {

$accion="Modificar_ele";
$cod=$_SESSION['id'];
$usuariox=$_SESSION['username'];

date_default_timezone_set('America/Caracas');
$fecha = date('d-m-Y');
$hora = strftime("%I:%M:%S %p\n");
         
$sql = "SELECT * FROM notas WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'"; 
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
while($fila = $resultado->fetch_assoc()) {   
    $codigo=$fila['codigo']; 
    $cod_mat=$fila['cod_mat']; 
    $nota=$fila['nota']; 
    $lapso=$fila['lapso']; 
    $tiplap=$fila['tiplap']; 
    $cod_doc=$fila['cod_doc']; 
    $cod_usu=$fila['cod_usu']; 
    $acu=$fila['acu']; 
    $hora=$fila['hora']; 
    $seccion=$fila['seccion']; 
    $electiva=$fila['electiva']; 
    $carrera = substr($_POST['cod_mat'], 0, 1);
    
    $sql = "INSERT INTO notas_auditoria (accion,cod,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,hora,seccion,electiva,fecha) VALUES ('$accion','$cod','$usuariox','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$hora','$seccion','$electiva','$fecha')";
    $result = $conn->query($sql); 
  
  }

}

$pensum=substr($cod_mat, 0, 1)."X".substr($cod_mat, 4, 1);

$sql = "INSERT INTO agregarseccion_auditoria(accion,cod,usuario,pensum,cod_mat,seccion,cod_doc,lapso,aula,descrip,hora,fecha,tipo,electiva) VALUES ('$accion','$cod','$usuariox','$pensum','$cod_mat','$seccion','$cod_doc','$lapso','$aula','$descrip','$hora','$fecha','$tipo','$electiva');";
$result = mysqli_query($conn, $sql);


 /////////****************////////////////         
  $sql = "UPDATE notas SET `electiva` = '".$electiva."' WHERE `cod_mat`='".$cod_mat."' AND `seccion`='".$seccion."' AND `cod_doc`='".$cod_doc."' AND `lapso`='".$lapso."'";

  //$sql = "UPDATE notas SET `electiva` = REPLACE(`electiva`, '".$electiva."', '".$electiva2."') WHERE `cod_mat`='".$cod_mat."' AND `seccion`='".$seccion."' AND `cod_doc`='".$cod_doc."' AND `lapso`='".$lapso."'";

  $result = $conn->query($sql);

  $sql = "UPDATE agregarseccion SET `electiva` = '".$electiva."' WHERE `cod_mat`='".$cod_mat."' AND `seccion`='".$seccion."' AND `cod_doc`='".$cod_doc."' AND `lapso`='".$lapso."'";

  $result = $conn->query($sql);
 /////////****************//////////////// 


  $sql = "SELECT notas.codigo,notas.cod_mat,notas.cod_doc,notas.lapso,notas.seccion,notas.electiva,alumno.nombre FROM notas,alumno where notas.electiva='".$electiva2."' and notas.cod_mat='".$cod_mat."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' and notas.cod_doc='".$cod_doc."' and notas.codigo=alumno.cedula";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      echo '<tr>  
      <td>'.$row["codigo"].'</td>       
      <td>'.$row["nombre"].'</td>               
      <td>'.$row["cod_mat"].'</td>
      <td>'.$row["electiva"].'</td>                 
      <td>'.$row["cod_doc"].'</td>                               
      <td>'.$row["lapso"].'</td>                    
      <td>'.$row["seccion"].'</td>                    
    </tr>';
  }
} 
}                               

$conn->close();   

?>


