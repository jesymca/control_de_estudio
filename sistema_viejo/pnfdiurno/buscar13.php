

       
<?php

include('db.php');

$carrera=substr($_POST["pensum"], 0, 1);
$seccion=$_POST["seccion"];
$lapso=$_POST["lapso"];



  $x1=1;
  $sql = "SELECT DISTINCT alumno.cedula,alumno.nombre,alumno.direccion,alumno.telefonoh,alumno.telefonoc,alumno.telefonot,alumno.email,alumno.actividad,notas.seccion FROM notas,alumno WHERE notas.codigo=alumno.cedula and notas.carrera='".$carrera."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' ORDER BY alumno.nombre ASC";
    
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo '<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="1%" style="background: #0C4783;color: #fff">N°</th>              
      <th width="1%" style="background: #0C4783;color: #fff">Cedula</th>
      <th width="10%" style="background: #0C4783;color: #fff">Nombre</th> 
      <th width="1%" style="background: #0C4783;color: #fff">Telefono</th>
      <th width="1%" style="background: #0C4783;color: #fff">Celular</th>
      <th width="1%" style="background: #0C4783;color: #fff">Telefono 2</th> 
      <th width="3%" style="background: #0C4783;color: #fff">Correo</th>         
    </tr>
  </thead>              
</table>';
    while($row = $result->fetch_assoc()) {
      $_SESSION['id']=$row["id"];
      echo '<tr>
      <td>'.$x1.'</td>         
      <td>'.$row["cedula"].'</td>
      <td>'.$row["nombre"].'</td> 
      <td>'.$row["telefonoh"].'</td> 
      <td>'.$row["telefonoc"].'</td> 
      <td>'.$row["telefonot"].'</td>
      <td>'.$row["email"].'</td>     
    </tr>';
    $x1++;    
   }
} 
$conn->close();
?>


