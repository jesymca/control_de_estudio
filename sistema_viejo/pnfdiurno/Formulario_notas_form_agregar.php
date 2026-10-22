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
$query = "SELECT * FROM notas WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $codigo = $row['codigo'];
  $cod_mat = $row['cod_mat'];
  $carrera = $row['carrera'];
  $nota = $row['nota'];
  $lapso = $row['lapso'];
  $tiplap = $row['tiplap'];
  $cod_doc = $row['cod_doc'];
  $cod_usu = $row['cod_usu'];
  $acu = $row['acu'];
  $seccion = $row['seccion'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar notas</label>
      </div>
      <p>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
            <label for="codigo">Codigo</label>  
            <input id="codigo" name="codigo" type="text" placeholder="Codigo" class="form-control">
          </div>
        </div>


        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="pensum">Pensum</label>  
            <input id="pensum" name="pensum" type="text" placeholder="Pensum" class="form-control" required style="width:50%" pattern="^[EXC|TXC|IXC|GXC|MXC]{3}$" title="Ejemplo: EXC ó TXC ó IXC ó GXC ó MXC" value="<?php echo $pensum;?>"> 


            <div class="selector-pensum" id="sp1">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div> 
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="cod_mat">Cod Mat</label>  
            <input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" class="form-control" required style="width:50%" value="<?php echo $cod_mat;?>">


            <div class="selector-cod_mat" id="sp2">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="seccion">Seccion</label>  
            <input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control" required style="width:50%" pattern="[01-99]{2}$" value="<?php echo $seccion;?>">

            <div class="selector-seccion" id="sp3">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div> 
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="cod_doc">Cod Doc</label>  
            <input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" required style="width:50%" pattern="[0-9]{1,4}$" value="<?php echo $cod_doc;?>">


            <div class="selector-cod_doc">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="lapso">Lapso</label>  
            <input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control" required style="width:50%" pattern="[0-9]{4}[-]{1}[1-3]{1}$" value="<?php echo $lapso;?>">


            <div class="selector-lapso" id="sp5">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div> 
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <div class="col-md-12" style="width: 80%;margin-left: 20px;margin-top:0px">
            <label for="tipo">Tipo</label>  
            <input id="tipo" name="tipo" type="text" placeholder="Tipo" class="form-control" style="width:50%" pattern="^[T|P]{1}$" value="<?php echo $tipo;?>">

            <div class="selector-tipo" id="sp6">   
              <select style="width:60%;height: 38px;margin-top:-38px;margin-left: 200px;position: absolute;"></select>      
            </div> 
          </div>
        </div>


      </p>


      <div class="form-group">
        <div class="col-md-12" style="margin-left: 20px;">

          <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
          <a href="Formulario_notas_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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
    $('#tipo').val('');
   

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
      buscarmateria($(".selector-pensum select").val());      
      $('#cod_mat').val('');
      $(".selector-cod_mat select").empty();
    }); 

    $.ajax({            
      type: "POST",
      url: "getdocente.php",        
      success: function(response)
      {
        $('.selector-cod_doc select').html(response).fadeIn();
      }
    });


    $('.selector-cod_doc select').click(function(){
      var v = $(this).val(); 
      $('#cod_doc').val(v);       
    }); 



  });
</script>

</body>
</html>


