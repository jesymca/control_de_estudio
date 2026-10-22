<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['planificacion']==1) {
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
$query = "SELECT * FROM planificacion WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $pensum = $row['pensum'];
  $cod_mat = $row['cod_mat'];
  $lapso = $row['lapso'];
  $seccion = $row['seccion'];
  $cod_doc = $row['cod_doc'];
  $cupo = $row['cupo'];
  $tipo = $row['tipo'];
  $electiva = $row['electiva'];
  $turno = $row['turno'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_planificacion_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar planificacion</label>
      </div>
      <p>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="pensum">Pensum</label>    
            <input type="hidden" id="pensum" name="pensum">       
            <input id="tipo" name="tipo" type="hidden">
            <div class="selector-pensum">   
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
            </div> 
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="cod_mat">Cod Mat</label>  
            <input type="hidden" id="cod_mat" name="cod_mat">
            <div class="selector-cod_mat">    
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>     
            </div>
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="electiva">Electiva</label>  
            <input type="hidden" id="electiva" name="electiva">
            <div class="selector-electiva">   
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>     
            </div>
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="lapso">Lapso</label>     
            <input type="hidden" id="lapso" name="lapso"> 
            <div class="selector-lapso">   
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>   
            </div> 
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="seccion">Seccion</label>      
            <input type="hidden" id="seccion" name="seccion"> 
            <div class="selector-seccion">   
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>    
            </div> 
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
            <label for="cod_doc">Cod Doc</label>      
            <input type="hidden" id="cod_doc" name="cod_doc"> 
            <div class="selector-cod_doc">   
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select> 
            </div>
          </div>
        </div>
        <br>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="cupo">Cupo</label>  
            <input id="cupo" name="cupo" type="text" placeholder="Cupo" class="form-control">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left: 19px;margin-top:0px">
            <label for="turno">Turno</label>  
            <input id="turno" name="turno" type="hidden">
            <div class="selector-turno"> 
              <select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
                <option value="">Seleccionar</option>
                <option value="Mañana">Mañana</option>
                <option value="Tarde">Tarde</option>
                <option value="Noche">Noche</option>
              </select>
            </div>

          </div>
        </div>
        <br> <br>

      </p>


      <div class="form-group">
        <div class="col-md-12" style="margin-left: 20px;">

          <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
          <a href="Formulario_planificacion_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
        </div>
      </div>
    </div>
  </fieldset>
</form>
</div>

<script>
  $(document).ready(function(){
    $('#pensum').val(''); 
    $('#cod_mat').val('');
    $('#seccion').val('');
    $('#lapso').val('');

    $.ajax({
      type: "POST",
      url: "getpensum.php",
      success: function(response)
      {
        $('.selector-pensum select').html(response).fadeIn();
      }
    });

    $('.selector-pensum select').change(function(){
      var v = $(this).val(); 
      $('#pensum').val(v);      
      buscarmateria($(".selector-pensum select").val());      
      $('#cod_mat').val('');
      $(".selector-cod_mat select").empty();
    }); 

    function buscarmateria(val1){
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
        }
      });
    }

    function buscarelectiva(val1,val2){
      var parametros = {
        "pensum":val1,
        "cod_mat":val2
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getelectivas.php",        
        success: function(response)
        {
          $('.selector-electiva select').html(response).fadeIn();
        }
      });
    }

    $('.selector-cod_mat select').change(function(){
      var v = $(this).val(); 
      $('#cod_mat').val(v);  
      buscarelectiva($(".selector-pensum select").val(),$('.selector-cod_mat select').val())     
    }); 

    $.ajax({
      type: "POST",
      url: "getnumseccion.php",
      success: function(response)
      {
        $('.selector-seccion select').html(response).fadeIn();
      }
    });

    $('.selector-seccion select').change(function(){
      var v = $(this).val(); 
      $('#seccion').val(v);       
    });

    $('.selector-electiva select').change(function(){
      var v = $(this).val(); 
      $('#electiva').val(v);       
    });

    $.ajax({            
      type: "POST",
      url: "getdocente_x3.php",        
      success: function(response)
      {
        $('.selector-cod_doc select').html(response).fadeIn();
      }
    });

    $('.selector-cod_doc select').change(function(){
      var v = $(this).val(); 
      $('#cod_doc').val(v);       
    }); 

    $.ajax({
      type: "POST",
      url: "getlapso2.php",
      success: function(response)
      {
        $('.selector-lapso select').html(response).fadeIn();
      }
    });

    $('.selector-lapso select').change(function(){
      var v = $(this).val(); 
      $('#lapso').val(v);       
    }); 

    $('.selector-turno select').change(function(){
      var v = $(this).val(); 
      $('#turno').val(v);       
    }); 

  });
</script>

</body>
</html>


