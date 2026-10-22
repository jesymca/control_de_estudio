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
$query = "SELECT * FROM horarios WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
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
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_horarios_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar horarios</label>
            </div>
<p>
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="id">Id</label>
    <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
    <input id="id1" name="id1" type="text" class="form-control" required value="<?php echo $id;?>">
  </div>
</div>

<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cod_mat">Cod Mat</label>  
    <input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" class="form-control" value="<?php echo $cod_mat;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="electiva">Electiva</label>  
    <input id="electiva" name="electiva" type="text" placeholder="Electiva" class="form-control" value="<?php echo $electiva;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="lapso">Lapso</label>  
    <input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control" value="<?php echo $lapso;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="seccion">Seccion</label>  
    <input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control" value="<?php echo $seccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cod_doc">Cod Doc</label>  
    <input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" value="<?php echo $cod_doc;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="aula">Aula</label>  
    <input id="aula" name="aula" type="text" placeholder="Aula" class="form-control" value="<?php echo $aula;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="descrip">Descrip</label>  
    <input id="descrip" name="descrip" type="text" placeholder="Descrip" class="form-control" value="<?php echo $descrip;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="hora_de_inicio">Hora De Inicio</label>  
    <input id="hora_de_inicio" name="hora_de_inicio" type="text" placeholder="Hora De Inicio" class="form-control" value="<?php echo $hora_de_inicio;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="hora_final">Hora Final</label>  
    <input id="hora_final" name="hora_final" type="text" placeholder="Hora Final" class="form-control" value="<?php echo $hora_final;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cantidad">Cantidad</label>  
    <input id="cantidad" name="cantidad" type="text" placeholder="Cantidad" class="form-control" value="<?php echo $cantidad;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="dia">Dia</label>  
    <input id="dia" name="dia" type="text" placeholder="Dia" class="form-control" value="<?php echo $dia;?>">
  </div>
</div>


</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_horarios_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
  </div>
</div>

     </div>
    </fieldset>
  </form>
</div>

<script>
  $(document).ready(function(){

  });
</script>

</body>
</html>


