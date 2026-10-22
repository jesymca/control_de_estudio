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
$id = intval($_GET['id']);
include 'db.php';
include 'menu.php';

$query = "SELECT * FROM agregarseccion WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
$pensum = $row['pensum'];
$cod_mat = $row['cod_mat'];
$seccion = $row['seccion'];
$cod_doc = $row['cod_doc'];
$lapso = $row['lapso'];
$electiva = $row['electiva'];
}
$conn->close();

?>
<div class="container" id="marco" style="width:400px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_edit.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Editar seccion</label>
      </div>
<p>
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
 
    <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="pensum">Pensum</label>     
  </div>
</div>
<br>
<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
     <input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
    <div class="selector-pensum" id="sp1">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left:0px;position: absolute;"></select>      
    </div> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="cod_mat">Cod Mat</label>  
    <input type="Text" class="form-control" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>" readonly style="background:white;width:43%;">
    <div class="selector-cod_mat" id="sp2">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left: 170px;position: absolute;"></select>      
    </div>
  </div>
</div>

<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="electiva">Electiva</label>  
    <input type="Text" class="form-control" id="electiva" name="electiva" value="<?php echo $electiva;?>" readonly style="background:white;width:43%;">
    <div class="selector-electiva" id="sp9">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left: 170px;position: absolute;"></select>   
    </div>
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="seccion">Seccion</label>      
    <input type="Text" class="form-control" id="seccion" name="seccion" value="<?php echo $seccion;?>" readonly style="background:white;width:43%;">
    <div class="selector-seccion" id="sp3">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left: 170px;position: absolute;"></select>   
    </div> 
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="cod_doc">Cod Doc</label>      
    <input type="Text" class="form-control" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>" readonly style="background:white;width:43%;">
    <div class="selector-cod_doc" id="sp4">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left: 170px;position: absolute;"></select>   
    </div>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
    <label for="lapso">Lapso</label>     
    <input type="Text" class="form-control" id="lapso" name="lapso" value="<?php echo $lapso;?>" readonly style="background:white;width:43%;">
    <div class="selector-lapso" id="sp5">   
      <select style="width:40%;height: 38px;margin-top:-38px;margin-left: 170px;position: absolute;"></select>   
    </div> 
  </div>
</div>

</p>
<br>

<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">
  
    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_agregarseccion_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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



    $('#sp1 select').change(function(){
      var v = $(this).val(); 
      $('#pensum').val(v);      
      buscarmateria($(".selector-pensum select").val());      
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


    $('#sp2 select').change(function(){
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

    $('#sp3 select').change(function(){
      var v = $(this).val(); 
      $('#seccion').val(v);       
    });

    $('#sp9 select').change(function(){
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


    $('#sp4 select').change(function(){
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

    $('#sp5 select').change(function(){
      var v = $(this).val(); 
      $('#lapso').val(v);       
    }); 

  });
</script>

</body>
</html>


