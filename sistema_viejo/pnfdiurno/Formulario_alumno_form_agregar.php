<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
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
$query = "SELECT * FROM alumno WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $codigo = $row['cedula'];
  $cedula = $row['cedula'];
  $nombre = $row['nombre'];
  $carrera = $row['carrera'];
  $mencion = $row['mencion'];
  $plan = $row['plan'];
  $actividad = $row['actividad'];
  $sexo = $row['sexo'];
  $edocivil = $row['edocivil'];
  $fechanac = $row['fechanac'];
  $edad = $row['edad'];
  $direccion = $row['direccion'];
  $telefonoh = $row['telefonoh'];
  $telefonoc = $row['telefonoc'];
  $telefonot = $row['telefonot'];
  $email = $row['email'];
  $ingreso = $row['ingreso'];
  $turno = $row['turno'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_alumno_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar alumno</label>
      </div>
        <p>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cedula">Cedula</label>  
    <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="nombre">Nombre</label>  
    <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="carrera">Carrera</label>  
    <input id="carrera" name="carrera" type="text" placeholder="Carrera" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="mencion">Mencion</label>  
    <input id="mencion" name="mencion" type="text" placeholder="Mencion" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="plan">Plan</label>  
    <input id="plan" name="plan" type="text" placeholder="Plan" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="actividad">Actividad</label>  
    <input id="actividad" name="actividad" type="text" placeholder="Actividad" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="sexo">Sexo</label>  
    <input id="sexo" name="sexo" type="text" placeholder="Sexo" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="edocivil">Edocivil</label>  
    <input id="edocivil" name="edocivil" type="text" placeholder="Edocivil" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="fechanac">Fechanac</label>  
    <input id="fechanac" name="fechanac" type="text" placeholder="Fechanac" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="edad">Edad</label>  
    <input id="edad" name="edad" type="text" placeholder="Edad" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="direccion">Direccion</label>  
    <input id="direccion" name="direccion" type="text" placeholder="Direccion" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="telefonoh">Telefonoh</label>  
    <input id="telefonoh" name="telefonoh" type="text" placeholder="Telefonoh" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="telefonoc">Telefonoc</label>  
    <input id="telefonoc" name="telefonoc" type="text" placeholder="Telefonoc" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="telefonot">Telefonot</label>  
    <input id="telefonot" name="telefonot" type="text" placeholder="Telefonot" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="email">Email</label>  
    <input id="email" name="email" type="text" placeholder="Email" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="ingreso">Ingreso</label>  
    <input id="ingreso" name="ingreso" type="text" placeholder="Ingreso" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="turno">Turno</label>  
    <input id="turno" name="turno" type="text" placeholder="Turno" class="form-control">
  </div>
</div>

        </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
    <a href="Formulario_alumno_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


