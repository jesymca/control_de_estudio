<?php
include 'db.php';
include 'menu.php';
?>
<style type="text/css">
.marco{
  margin:0px auto;  
  margin-top: 10px;
  width:400px;
}
</style>
  <form class="marco" class="form-horizontal" method="post" action="Formulario_agregarseccion_agregar.php">
  <div class="panel panel-primary">
  <div class="panel-heading">
  <h3 class="panel-title"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar seccion</h3>
  </div>
  <div class="panel-body">
                
               
  <div class="form-group">
    <label class="col-md-12" for="pensum" class="control-label">Pensum</label>   
    <div class="selector-pensum" class="col-md-12">   
        <input type="text" id="pensum" name="pensum" class="form-control" required>
    </div> 
  </div>


  <div class="form-group">
    <label for="cod_mat">Cod Mat</label>  
    <input type="hidden" id="cod_mat" name="cod_mat">
    <div class="selector-cod_mat" class="col-md-12">    
      <select class="form-control" required></select>     
    </div>
  </div>



  <div class="form-group">
    <label for="electiva">Electiva</label>  
    <input type="hidden" id="electiva" name="electiva">
    <div class="selector-electiva" class="col-md-12">   
      <select class="form-control"></select>     
    </div>
  </div>



  <div class="form-group">
    <label for="seccion">Seccion</label>      
    <input type="hidden" id="seccion" name="seccion"> 
    <div class="selector-seccion" class="col-md-12">   
      <select class="form-control" required></select>    
    </div> 
  </div>



  <div class="form-group">
    <label for="cod_doc">Cod Doc</label>      
    <input type="hidden" id="cod_doc" name="cod_doc"> 
    <div class="selector-cod_doc" class="col-md-12">   
      <select class="form-control" required></select> 
    </div>
  </div>



  <div class="form-group">
    <label for="lapso">Lapso</label>     
    <input type="hidden" id="lapso" name="lapso"> 
    <div class="selector-lapso" class="col-md-12">   
      <select class="form-control" required></select>   
    </div> 
  </div>

  
  <div class="col-md-12" style="margin-top: 10px;margin-left:-12px">
    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
    <a href="Formulario_agregarseccion_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Lista</a>
  </div>


</div>
</div>
</form>



<script>
  $(document).ready(function(){

    $('#pensum').val(''); 
    $('#cod_mat').val('');
    $('#seccion').val('');
    $('#lapso').val('');  

    var valor1 = {
        "opcion":"pensum"
    }

    $.ajax({
      data:valor1,
      type: "POST",
      url: "getselect.php",
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
      buscarseccion(v);
    }); 
    
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
          $('.selector-cod_mat select').html(response).fadeIn();
        }
      });
    }

    function buscarelectiva(val1,val2){
      var parametros = {
        "pensum":val1,
        "cod_mat":val2,
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


    $('.selector-cod_mat select').change(function(){
      var v = $(this).val(); 
      $('#cod_mat').val(v);  
      buscarelectiva($(".selector-pensum select").val(),$('.selector-cod_mat select').val())     
    }); 


    function buscarseccion(val1){
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
          $('.selector-seccion select').html(response).fadeIn();
        }
      });
    }

    $('.selector-seccion select').change(function(){
      var v = $(this).val(); 
      $('#seccion').val(v);       
    });

    $('.selector-electiva select').change(function(){
      var v = $(this).val(); 
      $('#electiva').val(v);       
    });

    var valor2 = {
        "opcion":"getdocente",
        "campos":"docente"
    }
    $.ajax({
      data:valor2,            
      type: "POST",
      url: "getselect.php",        
      success: function(response)
      {
        $('.selector-cod_doc select').html(response).fadeIn();
      }
    });

    $('.selector-cod_doc select').change(function(){
      var v = $(this).val(); 
      $('#cod_doc').val(v);       
    }); 

    var valor3 = {
        "opcion":"getlapso",
        "campos":"lapso"
    }
    $.ajax({
      data:valor3,
      type: "POST",
      url: "getselect.php",
      success: function(response)
      {
        $('.selector-lapso select').html(response).fadeIn();
      }
    });

    $('.selector-lapso select').change(function(){
      var v = $(this).val(); 
      $('#lapso').val(v);       
    });    

  });
</script>

</body>
</html>


