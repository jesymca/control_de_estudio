<?php
include 'db.php';
include 'menu.php';

$pensum=$_POST["pensum"];
$cod_doc=$_POST["cod_doc"];
$cod_mat=$_POST["cod_mat"];
$lapso=$_POST["lapso"];           
$seccion=$_POST["seccion"];                      
$cantidad=$_POST["cantidad"]; 


?>
<div class="container" id="marco" style="width:600px;">
  <form class="form-horizontal" id="effect2" method="post" action="buscar5.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-copy"></span> Seccion de destino</label>
      </div>

      <!--inicio de la Tabla-->
              <table class="responstable">
                <thead>
                  <tr>
                    <th width="1%">Pensum</th>
                    <th width="1%">Cod_doc</th>
                    <th width="1%">Cod_mat</th>
                    <th width="6%">Lapso</th>
                    <th width="1%">Seccion</th>
                    <th width="1%">Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                      echo '<tr>
                      <td>'.$pensum.'</td>
                      <td>'.$cod_doc.'</td>
                      <td>'.$cod_mat.'</td>
                      <td>'.$lapso.'</td>             
                      <td>'.$seccion.'</td>                      
                      <td>'.$cantidad.'</td>                                                    
                    </tr>';
                  ?>
              </tbody>
            </table>
            <!--Fin de la Tabla-->
            
<p>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:10px">
    <label for="cod_mat2">Cod Mat</label>  
    <input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
    <input type="hidden" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>">
    <input type="hidden" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>">
    <input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">
    <input type="hidden" id="seccion" name="seccion" value="<?php echo $seccion;?>">
    <input type="hidden" id="cantidad" name="cantidad" value="<?php echo $cantidad;?>">
    <input type="hidden" id="cod_mat2" name="cod_mat2">
    <div class="selector-cod_mat2">    
      <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>     
    </div>
  </div>
</div>
<br>
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:12px">
    <label for="electiva">Electiva</label>  
    <input type="hidden" id="electiva" name="electiva">
    <div class="selector-electiva">   
      <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>     
    </div>
  </div>
</div>
<br>
<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:12px">
    <label for="seccion2">Seccion</label>      
    <input type="hidden" id="seccion2" name="seccion2"> 
    <div class="selector-seccion2">   
      <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>    
    </div> 
  </div>
</div>
<br>
<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:12px">
    <label for="cod_doc2">Cod Doc</label>      
    <input type="hidden" id="cod_doc2" name="cod_doc2"> 
    <div class="selector-cod_doc2">   
      <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select> 
    </div>
  </div>
</div>
<br>
<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:12px">
    <label for="lapso2">Lapso</label>     
    <input type="hidden" id="lapso2" name="lapso2"> 
    <div class="selector-lapso2">   
      <select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>   
    </div> 
  </div>
</div>
<br>

</p>
<br>


<table>
                <tr>
                  <td>
                    <div class="form-group">
                      <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:-20px">
                        <input type="submit" id="btnbuscar3" class="btn btn-primary" name="submit" value="Copiar" style="background: #0C4783;width: 100px;"/> 
                        <a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
                      </div>



                      <div class="col-lg-10" style="position: absolute;margin-left: 245px;margin-top:-20px;width: 250px">
                        <div class="radio">
                          <label>
                            <input type="radio" name="Radios" id="optionsRadios1" value="A" checked="" style="width:20px">
                            Sin notas
                          </label>              

                          <label>
                            <input type="radio" name="Radios" id="optionsRadios2" value="B" style="width:20px;margin-left: -10px;">
                            Con notas
                          </label>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr> 
</table>
</div>
    </fieldset>
  </form>
</div>

<script>
  $(document).ready(function(){

    $('#pensum2').val(''); 
    $('#cod_mat2').val('');
    $('#seccion2').val('');
    $('#lapso2').val('');  
   
    buscarpensum();

    function buscarpensum(){
      var parametros = {
        "opcion":"pensum"
    }
    $.ajax({
      data:parametros,
      type: "POST",
      url: "getselect.php",
      success: function(response)
      {
        $('.selector-pensum2 select').html(response).fadeIn();
      }
    });
    }

    $('.selector-pensum2 select').change(function(){
      var v = $(this).val(); 
      $('#pensum2').val(v);      
           
      $('#cod_mat2').val('');
      $(".selector-cod_mat2 select").empty();
    }); 
    
    buscarmateria($('#pensum').val());

    buscarseccion2($('#pensum').val()); 

    function buscarmateria(val1){
      var parametros = {
        "pensum":val1,
        "opcion":"getmateria"
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getselect.php",        
        success: function(response)
        {
          $('.selector-cod_mat2 select').html(response).fadeIn();
        }
      });
    }

    function buscarelectiva(val1,val2){
      var parametros = {
        "pensum2":val1,
        "cod_mat2":val2,
        "opcion":"getelectivas"
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getselect.php",        
        success: function(response)
        {
          $('.selector-electiva select').html(response).fadeIn();
        }
      });
    }


    $('.selector-cod_mat2 select').change(function(){
      var v = $(this).val(); 
      $('#cod_mat2').val(v);  
      buscarelectiva($(".selector-pensum2 select").val(),$('.selector-cod_mat2 select').val())     
    }); 
    
   
    function buscarseccion2(val1){
      var parametros = {
        "pensum":val1,
        "opcion":"getnumseccion"
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getselect.php",        
        success: function(response)
        {
          $('.selector-seccion2 select').html(response).fadeIn();
        }
      });
    }

    $('.selector-seccion2 select').change(function(){
      var v = $(this).val(); 
      $('#seccion2').val(v);       
    });

    $('.selector-electiva select').change(function(){
      var v = $(this).val(); 
      $('#electiva').val(v);       
    });

    buscardocente2();

    function buscardocente2(val1){
      var parametros = {
        "opcion":"getdocente",
        "campos":"docente"
    }
    $.ajax({
      data:parametros,            
      type: "POST",
      url: "getselect.php",        
      success: function(response)
      {
        $('.selector-cod_doc2 select').html(response).fadeIn();
      }
    });
    }

    $('.selector-cod_doc2 select').change(function(){
      var v = $(this).val(); 
      $('#cod_doc2').val(v);       
    }); 
     
     buscarlapso2();

    function buscarlapso2(){
      var parametros = {
        "opcion":"getlapso",
        "campos":"lapso"
    }
    $.ajax({
      data:parametros,
      type: "POST",
      url: "getselect.php",
      success: function(response)
      {
        $('.selector-lapso2 select').html(response).fadeIn();
      }
    });
    }

    $('.selector-lapso2 select').change(function(){
      var v = $(this).val(); 
      $('#lapso2').val(v);       
    });    

  });
</script>

</body>
</html>


