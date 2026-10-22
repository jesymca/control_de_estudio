
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
                $_SESSION['id']=$row["id"];
                echo '<tr>
                <td>'.$row["id"].'</td>                       
                <td>'.$row["cedula"].'</td>
                <td>'.$row["nombre"].'</td> 
                <td>'.$row["carrera"].'</td> 
                <td>'.$row["mencion"].'</td>
                <td>'.$row["plan"].'</td> 
                <td>'.$row["actividad"].'</td> 
                <td><a href="CARGAR_MATERIAS_POR_TRAYECTO_FROM.php?id='.$row['id'].'"  data-toggle="tooltip" title="Mostrar datos" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span> </a></td><br>          
              </tr>';
            }
          } 
          $conn->close();
         }        
        ?>

        
