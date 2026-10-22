<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['aula']==1) {
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
$query = "SELECT * FROM aula WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $descrip = $row['descrip'];
  $aula = $row['aula'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_aula_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar aula</label>
      </div>
        <p>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="descrip">Descrip</label>  
    <input id="descrip" name="descrip" type="text" placeholder="Descrip" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="aula">Aula</label>  
    <input id="aula" name="aula" type="text" placeholder="Aula" class="form-control">
  </div>
</div>

        </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_aula_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


