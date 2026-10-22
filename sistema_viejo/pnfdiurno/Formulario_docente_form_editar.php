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
$id = intval($_GET['id']);
$query = "SELECT * FROM docente WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
$cod_doc = $row['cod_doc'];
$cedula = strtoupper($row['cedula']);
$nombre = strtoupper($row['nombre']);
$condicion = $row['condicion'];
$depart = $row['depart'];
$sexo = $row['sexo'];
$fechanac = $row['fechanac'];
$titulo_c = $row['titulo_c'];
$titulo_l = $row['titulo_l'];
$tipo = $row['tipo'];
$ingreso = $row['ingreso'];
$categoria = $row['categoria'];
$dedicacion = $row['dedicacion'];
$telefono = $row['telefono'];
$asignatura = $row['asignatura'];
$horas_ad = $row['horas_ad'];
$horas_do = $row['horas_do'];
$observa = $row['observa'];
$actividad = $row['actividad'];
$turno = $row['turno'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_docente_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar docente</label>
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
    <label for="cod_doc">Cod Doc</label>  
    <input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" value="<?php echo $cod_doc;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cedula">Cedula</label>  
    <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control" value="<?php echo $cedula;?>" required pattern="[V|E][0-9]{7,9}">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="nombre">Nombre</label>  
    <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control" value="<?php echo $nombre;?>" required>
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="condicion">Condicion</label>  
    <input id="condicion" name="condicion" type="text" placeholder="Condicion" class="form-control" value="<?php echo $condicion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="depart">Depart</label>  
    <input id="depart" name="depart" type="text" placeholder="Depart" class="form-control" value="<?php echo $depart;?>">
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
    <label for="fechanac">Fechanac</label>  
    <input id="fechanac" name="fechanac" type="text" placeholder="Fechanac" class="form-control" value="<?php echo $fechanac;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="titulo_c">Titulo C</label>  
    <input id="titulo_c" name="titulo_c" type="text" placeholder="Titulo C" class="form-control" value="<?php echo $titulo_c;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="titulo_l">Titulo L</label>  
    <input id="titulo_l" name="titulo_l" type="text" placeholder="Titulo L" class="form-control" value="<?php echo $titulo_l;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="tipo">Tipo</label>  
    <input id="tipo" name="tipo" type="text" placeholder="Tipo" class="form-control" value="<?php echo $tipo;?>">
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
    <label for="categoria">Categoria</label>  
    <input id="categoria" name="categoria" type="text" placeholder="Categoria" class="form-control" value="<?php echo $categoria;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="dedicacion">Dedicacion</label>  
    <input id="dedicacion" name="dedicacion" type="text" placeholder="Dedicacion" class="form-control" value="<?php echo $dedicacion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="telefono">Telefono</label>  
    <input id="telefono" name="telefono" type="text" placeholder="Telefono" class="form-control" value="<?php echo $telefono;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="asignatura">Asignatura</label>  
    <input id="asignatura" name="asignatura" type="text" placeholder="Asignatura" class="form-control" value="<?php echo $asignatura;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="horas_ad">Horas Ad</label>  
    <input id="horas_ad" name="horas_ad" type="text" placeholder="Horas Ad" class="form-control" value="<?php echo $horas_ad;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="horas_do">Horas Do</label>  
    <input id="horas_do" name="horas_do" type="text" placeholder="Horas Do" class="form-control" value="<?php echo $horas_do;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="observa">Observa</label>  
    <input id="observa" name="observa" type="text" placeholder="Observa" class="form-control" value="<?php echo $observa;?>">
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
    <label for="turno">Turno</label>  
    <input id="turno" name="turno" type="text" placeholder="Turno" class="form-control" value="<?php echo $turno;?>">
  </div>
</div>


</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_docente_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


