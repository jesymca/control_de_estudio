<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['docente']==1) {
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

$query = "SELECT max(cod_doc) as maxcod_doc FROM docente";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $maxcod_doc = $row['maxcod_doc']+1;  
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_docente_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar docente</label>
      </div>
        <p>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cod_doc">Cod Doc</label>  
    <input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" value="<?php echo $maxcod_doc;?>" readonly style="background:#F0F0F0">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cedula">Cedula</label>  
    <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control" required pattern="[V|E][0-9]{7,9}">
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
    <label for="condicion">Condicion</label>  
    <input id="condicion" name="condicion" type="text" placeholder="Condicion" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="depart">Depart</label>  
    <input id="depart" name="depart" type="text" placeholder="Depart" class="form-control">
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
    <label for="fechanac">Fechanac</label>  
    <input id="fechanac" name="fechanac" type="text" placeholder="Fechanac" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="titulo_c">Titulo C</label>  
    <input id="titulo_c" name="titulo_c" type="text" placeholder="Titulo C" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="titulo_l">Titulo L</label>  
    <input id="titulo_l" name="titulo_l" type="text" placeholder="Titulo L" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="tipo">Tipo</label>  
    <input id="tipo" name="tipo" type="text" placeholder="Tipo" class="form-control">
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
    <label for="categoria">Categoria</label>  
    <input id="categoria" name="categoria" type="text" placeholder="Categoria" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="dedicacion">Dedicacion</label>  
    <input id="dedicacion" name="dedicacion" type="text" placeholder="Dedicacion" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="telefono">Telefono</label>  
    <input id="telefono" name="telefono" type="text" placeholder="Telefono" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="asignatura">Asignatura</label>  
    <input id="asignatura" name="asignatura" type="text" placeholder="Asignatura" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="horas_ad">Horas Ad</label>  
    <input id="horas_ad" name="horas_ad" type="text" placeholder="Horas Ad" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="horas_do">Horas Do</label>  
    <input id="horas_do" name="horas_do" type="text" placeholder="Horas Do" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="observa">Observa</label>  
    <input id="observa" name="observa" type="text" placeholder="Observa" class="form-control">
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
    <label for="turno">Turno</label>  
    <input id="turno" name="turno" type="text" placeholder="Turno" class="form-control">
  </div>
</div>

        </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
    <a href="Formulario_docente_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


