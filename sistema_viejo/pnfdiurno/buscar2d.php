
<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="1%">Codigo</th>              
      <th width="1%">Cedula</th>
      <th width="10%">Nombre</th> 
      <th width="1%">Carrera</th>
      <th width="1%">Mencion</th> 
      <th width="1%">Plan</th> 
      <th width="1%">Actividad</th> 
      <th width="1%">Ver</th> 

    </tr>
  </thead>              
</table>

<?php
include('/Classes/class_api.php');
$pdf=new PDF();

require("db.php");
$buscar = Trim($_POST['buscar']);
if($buscar<>""){

  $sql = "SELECT * FROM alumno where cedula='".$buscar."' OR codigo='".$buscar."' OR nombre LIKE '%".$buscar."%'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $_SESSION['id']=$row["id"];

      echo '<tr>
      <td>'.$row["id"].'</td>         
      <td>'.$row["cedula"].'</td>
      <td>'.$row["nombre"].'</td> 
      <td>'?><?php echo $pdf->carrera_corta($row["carrera"]);?><?php
      echo '</td><td>'.$row["mencion"].'</td>
      <td>'.$row["plan"].'</td> 
      <td>'.$row["actividad"].'</td>
      <td><a href="SERVICIOS_H.php?id='.$row['id'].'  data-toggle="tooltip" title="" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span></span> </a></td><br>
    </tr>';    
  }
} 




$conn->close();
}

?>


