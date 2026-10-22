
          <table id="editable_table" class="table table-bordered table-striped">
            <thead>
              <tr>
               
                <th width="1%">Materia</th>
                <th width="1%">Cod_doc</th> 
                <th width="20%">Nombre del Docente</th>                                              
                <th width="10%">Lapso</th> 
                <th width="1%">Tipo</th>                 
                <th width="1%">Seccion</th>
                <th width="1%">Ver</th> 
                             
                     
              </tr>
            </thead>              
          </table>

          <?php
       
            $accion=$_POST['accion'];           
            $cod_mat = $_POST['cod_mat'];
            $cod_doc = $_POST['cod_doc'];            
            $lapso = $_POST['lapso'];
            $lapso2 = $_POST['lapso2'];
            $tiplap = $_POST['tiplap'];             
            $seccion = $_POST['seccion'];
           
 
            require("db.php");
        
          if($accion=="buscar") {                                                                                                                                     
            $sql = "SELECT notas.id,notas.cod_mat,notas.carrera,notas.cod_doc,notas.lapso,notas.tiplap,notas.seccion,docente.nombre  FROM notas,docente where notas.cod_mat='".$cod_mat."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' and notas.tiplap='".$tiplap."' and notas.cod_doc='".$cod_doc."' and notas.cod_doc=docente.cod_doc";
 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
               
                echo '<tr>                              
                <td>'.$row["cod_mat"].'</td>                 
                <td>'.$row["cod_doc"].'</td> 
                <td>'.$row["nombre"].'</td>               
                <td>'.$row["lapso"].'</td>
                <td>'.$row["tiplap"].'</td>                
                <td>'.$row["seccion"].'</td>         
              </tr>';
            }
          } 

         }elseif ($accion=="actualizar") {
           
            
 /////////****************////////////////         
            $sql = "UPDATE notas SET `lapso` = REPLACE(`lapso`, '".$lapso."', '".$lapso2."') WHERE `cod_mat`='".$cod_mat."' AND `lapso`='".$lapso."' AND `tiplap`='".$tiplap."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'";
 
            $result = $conn->query($sql);

            $sql = "UPDATE agregarseccion SET `lapso` = REPLACE(`lapso`, '".$lapso."', '".$lapso2."') WHERE `cod_mat`='".$cod_mat."' AND `lapso`='".$lapso."' AND `tipo`='".$tiplap."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'";
 
            $result = $conn->query($sql);
 /////////****************//////////////// 
            

            $sql = "SELECT notas.id,notas.cod_mat,notas.carrera,notas.cod_doc,notas.lapso,notas.tiplap,notas.seccion,docente.nombre  FROM notas,docente where notas.cod_mat='".$cod_mat."' and notas.seccion='".$seccion."' and notas.lapso='".$lapso."' and notas.tiplap='".$tiplap."' and notas.cod_doc='".$cod_doc2."' and notas.cod_doc=docente.cod_doc";
 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
               
                echo '<tr>                              
                <td>'.$row["cod_mat"].'</td>                 
                <td>'.$row["cod_doc"].'</td> 
                <td>'.$row["nombre"].'</td>               
                <td>'.$row["lapso"].'</td>
                <td>'.$row["tiplap"].'</td>                
                <td>'.$row["seccion"].'</td>                    
              </tr>';
            }
          } 
          }                               



          $conn->close();   

        ?>

        
