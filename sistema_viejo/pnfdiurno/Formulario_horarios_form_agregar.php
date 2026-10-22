<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['agregar_seccion']==1) {
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

$sql = "SELECT * FROM horarios WHERE id='".$id."'";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
while($fila = $resultado->fetch_assoc()) { 
  $id = $row['id'];
  $cod_mat = $row['cod_mat'];
  $electiva = $row['electiva'];
  $lapso = $row['lapso'];
  $seccion = $row['seccion'];
  $cod_doc = $row['cod_doc'];
  $aula = $row['aula'];
  $descrip = $row['descrip'];
  $hora_de_inicio = $row['hora_de_inicio'];
  $hora_final = $row['hora_final'];
  $cantidad = $row['cantidad'];
  $dia = $row['dia'];
}
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_horarios_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar horarios</label>
      </div>
        <p>

<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="pensum">Pensum</label>    
    <input type="hidden" id="pensum" name="pensum">
    <div class="selector-pensum">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
    </div> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="cod_mat">Cod Mat</label>  
    <input type="hidden" id="cod_mat" name="cod_mat">
    <div class="selector-cod_mat" id="sp2">    
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>     
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="electiva">Electiva</label>  
    <input type="hidden" id="electiva" name="electiva">
    <div class="selector-electiva" id="sp9">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>     
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="lapso">Lapso</label>     
    <input type="hidden" id="lapso" name="lapso"> 
    <div class="selector-lapso" id="sp5">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>   
    </div> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="seccion">Seccion</label>      
    <input type="hidden" id="seccion" name="seccion"> 
    <div class="selector-seccion">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
       
      </select>    
    </div> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="cod_doc">Cod Doc</label>      
    <input type="hidden" id="cod_doc" name="cod_doc"> 
    <div class="selector-cod_doc">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select> 
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="aula">Aula</label>     
    <input type="hidden" id="aula" name="aula">
    <div class="selector-aula" id="sp7">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 98%;margin-left: 20px;margin-top:40px">
    <label for="descrip">Descripción</label>  
    <input id="descrip" name="descrip" type="text" placeholder="Descrip" class="form-control" style="background: white;width:92%;" readonly>
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="hora_de_inicio">Hora de Inicio</label>     
    <input type="hidden" id="hora_de_inicio" name="hora_de_inicio">
    <div class="selector-hora_de_inicio">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:40px">
    <label for="cantidad">Cantidad de Horas</label>  
    <input id="cantidad" name="cantidad" type="hidden">
   <div class="selector-cantidad">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
        <option value="">Seleccionar</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>       
      </select>      
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 90%;margin-left: 20px;margin-top:40px">
    <label for="hora_final">Hora Final</label>     
    <input type="text" id="hora_final" name="hora_final" class="form-control" readonly style="background: white">
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:00px">
    <label for="dia">Dia</label>     
    <input type="hidden" id="dia" name="dia">
    <div class="selector-dia">   
      <select style="width:84%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
    </div>
  </div>
</div>

  </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;margin-top:40px">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_horarios_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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

    $('.selector-pensum select').change(function(){      
      var v = $(this).val(); 
      $('#pensum').val(v);       
       buscarmateria(v);      
       $('#cod_mat').val('');
       $(".selector-cod_mat select").empty();
       $('#seccion').val('');
       $(".selector-seccion select").empty();           
       buscarseccion(v);
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


    function buscarseccion(val1){
      var parametros = {
        "pensum":val1
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getnumseccion2.php",        
        success: function(response)
        {
          $('.selector-seccion select').html(response).fadeIn();
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

    $('.selector-seccion select').change(function(){
      var v = $(this).val(); 
      $('#seccion').val(v);       
    });

    $.ajax({
      type: "POST",
      url: "getlapso2.php",
      success: function(response)
      {
        $('.selector-lapso select').html(response).fadeIn();
      }
    });

    $.ajax({
      type: "POST",
      url: "getaula.php",
      success: function(response)
      {
        $('.selector-aula select').html(response).fadeIn();
      }
    });

    $.ajax({
      type: "POST",
      url: "gethora.php",
      success: function(response)
      {
        $('.selector-hora_de_inicio select').html(response).fadeIn();        
      }
    });

    $('.selector-hora_de_inicio select').change(function(){
      var v = $(this).val();       
      $('#hora_de_inicio').val(v); 
      $('#cantidad').val('');
      $(".selector-cantidad select").val('');      
      $('#hora_final').val('');
      $(".selector-hora_final select").empty();      
    });  

    $('.selector-hora_final select').change(function(){
      var v = $(this).val(); 
      $('#hora_final').val(v);       
    });  

    $.ajax({
      type: "POST",
      url: "getdia.php",
      success: function(response)
      {
        $('.selector-dia select').html(response).fadeIn();
      }
    });

    $('.selector-dia select').change(function(){
      var v = $(this).val(); 
      $('#dia').val(v);       
    }); 

    $('.selector-aula select').change(function(){
      var v = $(this).val();        
      $('#aula').val(v.substring(0, 4));  
      $('#descrip').val(v.substring(7, 35));      
    }); 

   
    $('.selector-lapso select').change(function(){
      var v = $(this).val(); 
      $('#lapso').val(v);       
    }); 

    $('.selector-cantidad select').change(function(){
      var v = $(this).val(); 
      $('#cantidad').val(v); 
      buscarhora_final($('#hora_de_inicio').val(),$('#cantidad').val());      
    }); 

    function buscarhora_final(val1,val2){
      var parametros = {
        "hora":val1,
        "cantidad":val2         
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "gethora_final.php",        
        success: function(response)
        {         
        	
          $('#hora_final').val(response); 
        }
      });
    }

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

  });
</script>

</body>
</html>


