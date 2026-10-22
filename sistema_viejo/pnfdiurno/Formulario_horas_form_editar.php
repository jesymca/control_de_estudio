<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['horas']==1) {
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
$query = "SELECT * FROM horas WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
$hora = $row['hora'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_horas_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar horas</label>
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
    <label for="hora">Hora</label>  
    <input id="hora" name="hora" type="text" placeholder="Hora" class="form-control" value="<?php echo $hora;?>">
  </div>
</div>


</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_horas_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


