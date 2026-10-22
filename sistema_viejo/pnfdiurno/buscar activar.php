
          <table id="editable_table" class="table table-bordered table-striped">
            <thead>
              <tr>
                 
                <th width="1%">Codigo</th>              
                <th width="1%">Cedula</th>
                <th width="40%">Nombre</th>                 
                <th width="1%">Actividad</th>                
                <th width="1%">Activar</th>  
                <th width="1%">Desactivar</th>                        
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
            $sql = "SELECT * FROM alumno where cedula='".$buscar."' OR codigo='".$buscar."' OR nombre LIKE '%".$buscar."%'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {               
                echo '<tr>
                <td>'.$row["id"].'</td>                       
                <td>'.$row["cedula"].'</td>
                <td>'.$row["nombre"].'</td>                
                <td>'.$row["actividad"].'</td> 
               
                <td><a href="activar.php?id='.$row['id'].'"  data-toggle="tooltip" title="Activar" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-thumbs-up"></span></span> </a></td><br> 
                 <td><a href="desactivar.php?id='.$row['id'].'"  data-toggle="tooltip" title="Desactivar" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-thumbs-down"></span></span> </a></td><br>                   
              </tr>';
            }
          } 
          $conn->close();
         }    

?>
