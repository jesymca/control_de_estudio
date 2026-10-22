<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas']==1) {
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
$query = "SELECT * FROM noticias WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $titulo = $row['titulo'];
  $contenido = $row['contenido'];
  $color = $row['color'];
  $icono = $row['icono'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_noticias_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar noticias</label>
      </div>
      <p>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="titulo">Titulo</label>  
            <input id="titulo" name="titulo" type="text" placeholder="Titulo" class="form-control" required>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="contenido">Contenido</label>  
            <input id="contenido" name="contenido" type="text" placeholder="Contenido" class="form-control" required>
          </div>
        </div>

        <!-- Text input-->
         <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="color">Color</label>  
            <input type="hidden" id="color" name="color">
            <div class="selector-color">   
              <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;background: #F0F0F0" required>
               <option value="">Seleccionar</option>
                <option value="default">Blanco</option>
                <option value="primary">Azul</option>
                <option value="danger">Naranja</option>
                <option value="warning">Rojo</option>
                <option value="success">Verde</option>
                <option value="info">Morado</option>
              </select>     
            </div>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
            <label for="icono">Icono</label>
            <div class="selector-icono"> 
            <select class="form-control" name="icono" style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;background: #F0F0F0">
            </select>
            </div>            
          </div>
        </div>

      </p>


      <div class="form-group">
        <div class="col-md-12" style="margin-left: 20px;margin-top:50px">

          <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
          <a href="Formulario_noticias_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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
      url: "geticonos.php",
      success: function(response)
      {
        $('.selector-icono select').html(response).fadeIn();
      }
    });

    $('.selector-icono select').change(function(){
      var v = $(this).val(); 
      $('#icono').val(v);       
    });

    $('.selector-color select').change(function(){
      var v = $(this).val(); 
      $('#color').val(v);       
    });

  });
</script>

</body>
</html>


