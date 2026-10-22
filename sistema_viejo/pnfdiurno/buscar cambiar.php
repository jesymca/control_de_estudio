
          <table class="responstable">
            <thead>
              <tr>                 
                <th width="1%">Codigo</th>         
                <th width="13%">Cedula</th>
                <th width="55%">Nombre</th>                
                <th width="10%">Carrera</th>
                <th width="10%">Editar</th>                                  
              </tr>
        
      </thead>
    

      <?php
       
            require("db.php");
            $buscar = $_POST['buscar'];
            if($buscar<>""){
            
            $sql = "SELECT * FROM alumno where cedula='".$buscar."' OR nombre LIKE '%".$buscar."%'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) { 
      ?>


     <tr><td><?php echo $row["id"]; ?></td>          
     <td><?php echo $row["cedula"]; ?></td>
     <td><?php echo $row["nombre"]; ?></td>
     <td><?php echo $row["carrera"]; ?></td>      
     <td><a href="editar_carrera.php?action=editar&id=<?php echo $row["id"]; ?>"  data-toggle="tooltip" title="Editar datos" class="btn btn-sm btn-info" style="background: #FF9800;width:45px"><span class="glyphicon glyphicon-pencil"></span></a></td>
     </td>                 
     </tr>
     <?php            
               
            }
          } 
          $conn->close();
         }    

?>
  
</table>
<script src="js/jquery-3.1.1.min3.js" type="text/javascript"></script>
<script>


 buscarmateria($('#pensum').val());

 function buscarmateria(val1){
   //show_loading_message();
   var parametros = {
    "pensum":val1             
   }              
   $.ajax({
    data:parametros,
    type: "POST",
    url: "getmateria.php",        
    success: function(response)
    {                 
      $('.selector-cod_mat select').html(response).fadeIn();
     //hide_loading_message();
  }
});
}

</script>