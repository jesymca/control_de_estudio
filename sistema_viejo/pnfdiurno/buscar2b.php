
<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="1%">#</th>         
      <th width="1%">Cedula</th>
      <th width="10%">Nombre</th> 
      <th width="1%">Nota</th>
      <th width="1%">Cod_mat</th> 
      <th width="1%">Lapso</th>
      <th width="1%">Cod_doc</th>
      <th width="1%">Seccion</th>
      <th width="1%">Actividad</th> 

    </tr>
  </thead>              
</table>

<?php

require("db.php");

$cod_doc = $_POST['cod_doc'];
$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$seccion = $_POST['seccion'];



$sql = "SELECT alumno.cedula,alumno.nombre,notas.nota,notas.cod_mat,notas.lapso,notas.cod_doc,notas.seccion,alumno.actividad FROM notas,alumno where notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."' and notas.cod_doc='".$cod_doc."' and notas.seccion='".$seccion."' and notas.codigo=alumno.cedula";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $cantidad=1;
  while($row = $result->fetch_assoc()) {
    $_SESSION['id']=$row["id"];


    echo '<tr>
    <td>'.$cantidad.'</td>      
    <td>'.$row["cedula"].'</td>
    <td>'.$row["nombre"].'</td>
    <td>'.$row["nota"].'</td> 
    <td>'.$row["cod_mat"].'</td>
    <td>'.$row["lapso"].'</td>
    <td>'.$row["cod_doc"].'</td>
    <td>'.$row["seccion"].'</td> 
    <td>'.$row["actividad"].'</td> 
  </tr>';    
  $cantidad=$cantidad+1;
 
}
} 

$conn->close();
?>


