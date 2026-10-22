<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['electivas']==1) {
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
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_electivas_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar electivas</label>
      </div>
      <p>

        <!-- Text input-->
         <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="cod_ele">Cod Ele</label>    
            <input type="hidden" id="cod_ele" name="cod_ele">
            <div class="selector-cod_ele">   
              <select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>      
            </div> 
          </div>
        </div>

        <br>
        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 85%;margin-left: 20px;margin-top:20px">
            <label for="descrip2">Descrip2</label>  
            <input id="descrip2" name="descrip2" type="text" placeholder="Descrip2" class="form-control">
          </div>
        </div>

        <!-- Text input-->

        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="pensum">Pensum</label>    
            <input type="hidden" id="pensum" name="pensum">
            <div class="selector-pensum">   
              <select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>      
            </div> 
          </div>
        </div>
        <br>


      </p>


      <div class="form-group">
        <div class="col-md-12" style="margin-left: 20px;">

          <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
          <a href="Formulario_electivas_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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
        $('.selector-pensum select').html(response).fadeIn();
      }
    });

    $('.selector-pensum select').click(function(){
      var v = $(this).val(); 
      $('#pensum').val(v); 
    }); 


    $.ajax({
      type: "POST",
      url: "getcod_electiva.php",
      success: function(response)
      {
        $('.selector-cod_ele select').html(response).fadeIn();
      }
    });

    $('.selector-cod_ele select').click(function(){
      var v = $(this).val(); 
      $('#cod_ele').val(v); 
    }); 

  });
</script>

</body>
</html>


