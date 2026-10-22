<!DOCTYPE html>
<html lang="es">
<head>
  
  <title>Control de Estudio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
  <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta charset="utf-8">

  <link rel="stylesheet" href="css/bootstrap.css" media="screen">
  <link rel="stylesheet" href="css/sweetalert.css">
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>        
  <link type="text/css" href="css/theme.css" rel="stylesheet">        
  <link rel="stylesheet" href="css/style.css" media="screen"> 


  <script src="js/jquery-3.1.1.min3.js"></script>
  <script src="js/jquery-1.9.1.min.js"></script>
  <script src="js/jquery-1.10.2.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap.min.js"></script>
  <script src="js/jquery.tabledit.js"></script>  
  <script src="js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>   
  <script src="js/jquery.dataTables.js"></script>
  <script src="js/dataTables.bootstrap.js"></script>
  <script src="js/sweetalert-dev.js"></script> 

  <style type="text/css">body
    {
      /*background-image: url("fondo.jpg");*/
      background-attachment: fixed;
      background-size:cover;
      background-repeat:no-repeat;
      background-position: center center;

    }
  </style>
</head>
<body>

<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="1%">Id</th>
      <th width="1%">Seccion</th>
      <th width="1%">Cedula</th>
      <th width="45%">Nombre</th>
      <th width="7%">Lapso</th>
      <th width="1%">Tipo</th>
      <th width="1%">Docente</th>
      <th width="1%">Nota</th>
      <th width="1%">Acu</th>      
      <th width="1%">Eliminar</th>
    </tr>
  </thead>
  <tbody id="table-body">

    <?php

    require("db.php");

    $sql = "SELECT notas.id,notas.cod_mat,notas.codigo,notas.nota,notas.acu,notas.lapso,notas.tiplap,notas.cod_doc,alumno.cedula,alumno.nombre,inscribirmateria.alumno FROM notas,inscribirmateria,alumno where alumno.cedula=notas.codigo and notas.codigo=inscribirmateria.alumno and notas.cod_doc='".$_POST["docente"]."' and notas.cod_mat='".$_POST["seccion"]."' and notas.lapso='".$_POST["lapso"]."' and notas.tiplap='".$_POST["tiplap"]."'"; 
    $resultado = $conn->query($sql); 

    $x=1;
    if ($resultado->num_rows > 0) {
     while($row = $resultado->fetch_assoc()) { 

      echo '<tr id="table-row-'.$row["id"].'">     
      <td contenteditable="false" onBlur="saveToDatabase(this,"x",'.$x.')" onClick="editRow(this);">'.$x.'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"id",'.$row["id"].')" onClick="editRow(this);">'.$row["id"].'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"cod_mat",'.$row["cod_mat"].')" onClick="editRow(this);">'.$row["cod_mat"].'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"codigo",'.$row["codigo"].')" onClick="editRow(this);">'.$row["codigo"].'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"nombre",'.$row["nombre"].')" onClick="editRow(this);">'.$row["nombre"].'</td>      
      <td contenteditable="false" onBlur="saveToDatabase(this,"lapso",'.$row["lapso"].')" onClick="editRow(this);">'.$row["lapso"].'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"tiplap",'.$row["tiplap"].')" onClick="editRow(this);">'.$row["tiplap"].'</td>
      <td contenteditable="false" onBlur="saveToDatabase(this,"cod_doc",'.$row["cod_doc"].')" onClick="editRow(this);">'.$row["cod_doc"].'</td>
      <td contenteditable="true" onBlur="saveToDatabase(this,"nota",'.$row["nota"].')" onClick="editRow(this);">'.$row["nota"].'</td>
      <td contenteditable="true" onBlur="saveToDatabase(this,"acu",'.$row["acu"].')" onClick="editRow(this);">'.$row["acu"].'</td>
      <td><button type="button" class="ajax-action-links" onclick="deleteRecord('.$row["cod_doc"].');" class="btn btn-danger btn-xs delete" style="background: #2196F3;color:#F0F0F0;width:35px"><span class="glyphicon glyphicon-trash"></span></button></td>      
     </tr>';
     $x++;				

  }
}






/////////////////////////////////////////////


  

//<th>'.'<button type="button" name="update" id="'.$row["id"].'" class="btn btn-warning btn-xs update" style="background: #7F007F;width:50px"><span class="glyphicon glyphicon-pencil"></span></button>'.'</td>
//<th>'.'<button type="button" name="delete" id="'.$row["id"].'" class="btn btn-danger btn-xs delete" style="background: #FF0000;width:50px"><span class="glyphicon glyphicon-trash"></span></button>'.'</td>      


 

?>
  </tbody>
</table>