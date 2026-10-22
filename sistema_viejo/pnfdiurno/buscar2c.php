


<?php

require("db.php");
$cedula = $_POST['cedula'];
$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$seccion = $_POST['seccion'];

$sql = "SELECT notas.codigo,notas.nota,notas.cod_mat,notas.lapso,notas.tiplap,notas.cod_doc,notas.seccion,lismat.descrip2 FROM notas,lismat where notas.lapso='".$lapso."' and notas.codigo='".$cedula."' and lismat.cod_mat=notas.cod_mat";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  echo '<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>       
      <th width="1%">Cedula</th>  
      <th width="1%">Nota</th>  
      <th width="1%">Cod_mat</th>
      <th width="10%">Descripcion</th>      
      <th width="1%">Lapso</th>
      <th width="1%">Tiplap</th>
      <th width="1%">Cod_doc</th> 
      <th width="1%">Seccion</th> 
       
    </tr>
  </thead>              
</table>';
  while($row = $result->fetch_assoc()) {
    $_SESSION['id']=$row["id"];
    echo '<tr>        
    <td>'.$row["codigo"].'</td>
    <td>'.$row["nota"].'</td>
    <td>'.$row["cod_mat"].'</td> 
    <td>'.$row["descrip2"].'</td>   
    <td>'.$row["lapso"].'</td>
    <td>'.$row["tiplap"].'</td>
    <td>'.$row["cod_doc"].'</td>    
    <td>'.$row["seccion"].'</td>     
  </tr>';    
  
}
} 

$conn->close();
?>


