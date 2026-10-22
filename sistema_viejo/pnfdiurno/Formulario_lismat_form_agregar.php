<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['lismat']==1) {
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
$query = "SELECT * FROM lismat WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $pensum = $row['pensum'];
  $cod_mat = $row['cod_mat'];
  $descrip2 = $row['descrip2'];
  $creditos = $row['creditos'];
  $aprobatori = $row['aprobatori'];
  $semestre = $row['semestre'];
  $trayecto = $row['trayecto'];
  $divicion = $row['divicion'];
  $nota = $row['nota'];
  $cod_mat_libro_rector = $row['cod_mat_libro_rector'];
  $cod_mat_ant = $row['cod_mat_ant'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_lismat_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar Pensum</label>
      </div>
        <p>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="pensum">Pensum</label>  
    <input id="pensum" name="pensum" type="text" placeholder="Pensum" class="form-control"  pattern="[A-Z]{1}[X]{1}[A-Z]{1}" title="Ejemplo: EXC" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cod_mat">Cod Mat</label>  
    <input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="descrip2">Descrip2</label>  
    <input id="descrip2" name="descrip2" type="text" placeholder="Descrip2" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="creditos">Creditos</label>  
    <input id="creditos" name="creditos" type="text" placeholder="Creditos" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="aprobatori">Aprobatori</label>  
    <input id="aprobatori" name="aprobatori" type="text" placeholder="Aprobatori" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="semestre">Semestre</label>  
    <input id="semestre" name="semestre" type="text" placeholder="Semestre" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="trayecto">Trayecto</label>  
    <input id="trayecto" name="trayecto" type="text" placeholder="Trayecto" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="divicion">Divicion</label>  
    <input id="divicion" name="divicion" type="text" placeholder="Divicion" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="nota">Nota</label>  
    <input id="nota" name="nota" type="text" placeholder="Nota" class="form-control" required>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cod_mat_libro_rector">Cod Mat Libro Rector</label>  
    <input id="cod_mat_libro_rector" name="cod_mat_libro_rector" type="text" placeholder="Cod Mat Libro Rector" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cod_mat_ant">Cod Mat Ant</label>  
    <input id="cod_mat_ant" name="cod_mat_ant" type="text" placeholder="Cod Mat Ant" class="form-control">
  </div>
</div>

        </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
    <a href="Formulario_lismat_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


