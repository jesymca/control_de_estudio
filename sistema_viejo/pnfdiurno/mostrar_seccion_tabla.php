   <!--inicio de la Tabla-->
   <table class="responstable">
    <thead>
      <tr>     
        <th width="1%">#</th> 
        <th width="1%">Id</th>  
        <th width="10%">Cedula</th>
        <th width="60%">Nombre</th>        
        <th width="1%">Cod_mat</th>
        <th width="10%">Lapso</th>
        <th width="1%">Seccion</th>
        <th width="1%">Cod_doc</th>
        <th width="1%">Nota</th>
        <th width="1%">Ver</th> 

      </tr>
    </thead>
    <tbody>

      <?php

      session_start();                                                  
      if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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
     $cod_mat=$_POST['cod_mat'];
     $lapso=$_POST['lapso'];
     $seccion=$_POST['seccion'];
    

     include "db.php";
     $sql = "SELECT alumno.id,notas.codigo,alumno.nombre,notas.cod_mat,notas.lapso,notas.seccion,notas.cod_doc,notas.nota FROM notas,alumno WHERE notas.codigo=alumno.cedula and notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."' and notas.seccion='".$seccion."'";
     $result = $conn->query($sql);
     $x1=$x1+1;
     if ($result->num_rows > 0) {
      while($fila = $result->fetch_assoc()) {
        echo '<tr> 
        <td>'.$x1.'</td> 
        <td>'.$fila["id"].'</td>        
        <td>'.$fila["codigo"].'</td> 
        <td>'.$fila["nombre"].'</td>                 
        <td>'.$fila["cod_mat"].'</td>
        <td>'.$fila["lapso"].'</td>
        <td>'.$fila["seccion"].'</td> 
        <td>'.$fila["cod_doc"].'</td>  
        <td>'.$fila["nota"].'</td>
        <td><a href="Formulario_notas_tabla_index.php?id='.$fila['id'].'"  data-toggle="tooltip" title="Mostrar Todas las Notas" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span> </a></td><br>                                                         
      </tr>';
       $x1=$x1+1;
    }
  }
  $conn->close();


  ?>
</tbody>
</table>









