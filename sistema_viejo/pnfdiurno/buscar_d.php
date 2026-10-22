
          <table id="editable_table" class="table table-bordered table-striped">
            <thead>
              <tr>                 
                <th width="1%">Cod Doc</th>
                <th width="1%">Codigo</th>
                <th width="1%">Cod_mat</th>
                <th width="1%">lapso</th>                
                <th width="1%">Ver</th>
              
              </tr>
            </thead>              
          </table>

          <?php
       
            require("db.php");
            $buscar = $_POST['buscar'];
            if($buscar<>""){
            $conn = new mysqli($servidor, $usuario, $clave, $base_datos);
            if ($conn->connect_error) {
              die("Conexion Fallida: " . $conn->connect_error);
            }
            $sql = "SELECT * FROM notas where cod_doc='".$buscar."' OR cod_mat='".$buscar."'";// OR docente.nombre LIKE '%".$buscar."%'
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                $_SESSION['id']=$row["id"];
                echo '<tr>                                
                <td>'.$row["cod_doc"].'</td>
                <td>'.$row["codigo"].'</td>                
                <td>'.$row["cod_mat"].'</td> 
                <td>'.$row["lapso"].'</td>  
                           
                <td><a href="Formulario_notas_tabla_index.php?id='.$row['id'].'"  data-toggle="tooltip" title="Mostrar datos" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span></span> </a></td><br>          
              </tr>';
            }
          } 
          $conn->close();
         }        
        ?>

        
