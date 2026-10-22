<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['lapso']==1) {
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
include 'db.php';
include 'menu.php';
$id = intval($_GET['id']);
$query = "SELECT * FROM lapso WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $lapso = $row['lapso'];
  $descrip = $row['descrip'];
  $carrera = $row['carrera'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_lapso_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar lapso</label>
      </div>
      <p>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="lapso">Lapso</label>  
            <input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control" pattern="[0-9]{4}-[1-3]" required title="Ejemplo: 2019-3">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="descrip">Descrip</label>  
            <input id="descrip" name="descrip" type="text" placeholder="Descrip" class="form-control" required>
          </div>
        </div>


        <div class="form-group">
          <div class="col-md-12" style="width:0%;margin-left: 20px;">
            <label for="carrera">Carrera</label>
            <input type="hidden" id="carrera" name="carrera">                     
          </div>

          <div class="selector-carrera" id="sp0">   
            <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select>      
          </div>
        </div>



      </p>


      <div class="form-group">
        <div class="col-md-12" style="margin-left: 20px;">

          <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
          <a href="Formulario_lapso_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
        </div>
      </div>
    </div>
  </fieldset>
</form>
</div>

<script>
  $(document).ready(function(){

    $.ajax({
      type: "POST",
      url: "getpensum.php",
      success: function(response)
      {
        $('.selector-carrera select').html(response).fadeIn();
      }
    });

    $('.selector-carrera select').click(function(){
            var v = $(this).val(); 
            $('#carrera').val(v);      
    }); 

  });
</script>

</body>
</html>


