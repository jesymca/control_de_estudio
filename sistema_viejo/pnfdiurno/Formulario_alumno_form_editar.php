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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_alumno_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar alumno</label>
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
    <label for="cedula">Cedula</label>  
    <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control" value="<?php echo $cedula;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="nombre">Nombre</label>  
    <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control" value="<?php echo $nombre;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="carrera">Carrera</label>  
    <input id="carrera" name="carrera" type="text" placeholder="Carrera" class="form-control" value="<?php echo $carrera;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="mencion">Mencion</label>  
    <input id="mencion" name="mencion" type="text" placeholder="Mencion" class="form-control" value="<?php echo $mencion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="plan">Plan</label>  
    <input id="plan" name="plan" type="text" placeholder="Plan" class="form-control" value="<?php echo $plan;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="actividad">Actividad</label>  
    <input id="actividad" name="actividad" type="text" placeholder="Actividad" class="form-control" value="<?php echo $actividad;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="sexo">Sexo</label>  
    <input id="sexo" name="sexo" type="text" placeholder="Sexo" class="form-control" value="<?php echo $sexo;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="edocivil">Edocivil</label>  
    <input id="edocivil" name="edocivil" type="text" placeholder="Edocivil" class="form-control" value="<?php echo $edocivil;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="fechanac">Fechanac</label>  
    <input id="fechanac" name="fechanac" type="text" placeholder="Fechanac" class="form-control" value="<?php echo $fechanac;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="edad">Edad</label>  
    <input id="edad" name="edad" type="text" placeholder="Edad" class="form-control" value="<?php echo $edad;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="direccion">Direccion</label>  
    <input id="direccion" name="direccion" type="text" placeholder="Direccion" class="form-control" value="<?php echo $direccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="telefonoh">Telefonoh</label>  
    <input id="telefonoh" name="telefonoh" type="text" placeholder="Telefonoh" class="form-control" value="<?php echo $telefonoh;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="telefonoc">Telefonoc</label>  
    <input id="telefonoc" name="telefonoc" type="text" placeholder="Telefonoc" class="form-control" value="<?php echo $telefonoc;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="telefonot">Telefonot</label>  
    <input id="telefonot" name="telefonot" type="text" placeholder="Telefonot" class="form-control" value="<?php echo $telefonot;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="email">Email</label>  
    <input id="email" name="email" type="text" placeholder="Email" class="form-control" value="<?php echo $email;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="ingreso">Ingreso</label>  
    <input id="ingreso" name="ingreso" type="text" placeholder="Ingreso" class="form-control" value="<?php echo $ingreso;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="turno">Turno</label>  
    <input id="turno" name="turno" type="text" placeholder="Turno" class="form-control" value="<?php echo $turno;?>">
  </div>
</div>


</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_alumno_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


