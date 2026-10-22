
<table id="editable_table" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th width="1%">#</th> 
        <th width="1%">Alumno</th>
        <th width="20%">Nombre</th>          
        <th width="1%">Materia</th>          
        <th width="1%">Nota</th> 
        <th width="1%">acu</th>  
        <th width="1%">Lapso</th>        
        <th width="1%">Seccion</th>  
        <th width="1%">Docente</th>  
                                 
    </tr>
</thead>              
</table>


<?php

require("db.php");


$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$cod_doc = $_POST['cod_doc'];
$seccion = $_POST['seccion']; 


$sql = "SELECT alumno.nombre,alumno.cedula,notas_copiar_materia.codigo,notas_copiar_materia.cod_mat,notas_copiar_materia.carrera,notas_copiar_materia.nota,notas_copiar_materia.acu,notas_copiar_materia.lapso,notas_copiar_materia.tiplap,notas_copiar_materia.seccion,notas_copiar_materia.cod_doc,notas_copiar_materia.cod_usu FROM notas_copiar_materia,alumno WHERE notas_copiar_materia.cod_mat='".$cod_mat."' AND notas_copiar_materia.lapso='".$lapso."' AND notas_copiar_materia.cod_doc='".$cod_doc."' AND notas_copiar_materia.seccion='".$seccion."' and notas_copiar_materia.codigo=alumno.cedula"; 
$result = $conn->query($sql);
$n=1;

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {                
    echo '<tr>
    <td>'.$n.'</td>  
    <td>'.$row["codigo"].'</td> 
    <td>'.$row["nombre"].'</td>         
    <td>'.$row["cod_mat"].'</td>   
    <td>'.$row["nota"].'</td> 
    <td>'.$row["acu"].'</td> 
    <td>'.$row["lapso"].'</td>    
    <td>'.$row["seccion"].'</td> 
    <td>'.$row["cod_doc"].'</td>           
</tr>';



$n++;
}
} 


$conn->close(); 


?>


