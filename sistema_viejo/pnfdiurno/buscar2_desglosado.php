
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
                <th width="1%">Tsu</th> 
                <th width="1%">Ing</th>               
              </tr>
            </thead>              
          </table>

          <?php
       
            require("db.php");
            $buscar = $_POST['buscar'];
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
                <td>'.$row["carrera"].'</td> 
                <td>'.$row["mencion"].'</td>
                <td>'.$row["plan"].'</td> 
                <td>'.$row["actividad"].'</td>
                <td><a href="HISTORIAL ACADEMICO_TSU_DESGLOSADO.php?id='.$row['id'].'"  data-toggle="tooltip" title="TSU" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span></span> </a></td><br>
                <td><a href="HISTORIAL ACADEMICO_ING_DESGLOSADO.php?id='.$row['id'].'"  data-toggle="tooltip" title="INGINIERO" class="btn btn-sm btn-primary"> <span class="glyphicon glyphicon-eye-open"></span></span> </a></td><br>                    

              </tr>';
            }
          } 
          $conn->close();
         }
        //<!--<td><button type="button" name="ver" id='.$row["id"].' class="btn btn-warning btn-xs update" onClick="prueba('.$row["id"].')">Ver</button></td><br>                              
        ?>

        
