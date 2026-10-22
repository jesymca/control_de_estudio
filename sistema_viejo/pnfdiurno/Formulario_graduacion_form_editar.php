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



include 'menu.php';
$cedula=$_POST["cedula"];
$grado=$_POST["grado"];


include 'db.php';
$query = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
$id = $row['id'];
$nombre = $row['nombre'];
$marca = $row['marca'];
$fe_gr_alu = $row['fe_gr_alu'];
}


?>
<div class="form-group" id="marco" style="width:500px;">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_graduacion_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar alumno</label>
            </div>
<p>
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="id">Id</label>
    <input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula?>">
    <input type="hidden" id="grado" name="grado" value="<?php echo $grado?>">
    <input id="id1" name="id1" type="text" class="form-control" required value="<?php echo $id;?>" readonly style="background: white">
  </div>
</div>

<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cedula">Cedula</label>  
    <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control" value="<?php echo $cedula;?>" readonly style="background: white">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="nombre">Nombre</label>  
    <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control" value="<?php echo $nombre;?>" readonly style="background: white">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-6" style="width: 30%;margin-left: 0%;margin-top:0px">
    <label for="marca">Marca</label>  
    <input id="marca" name="marca" type="text" placeholder="Marca" class="form-control" value="<?php echo $marca;?>" style="background: white">
  </div>

    <div class="col-md-6" style="margin-left: 0%;margin-top:0px">
    <label for="pnf">Tipo de estudio</label>  
    <input id="tipo_estudio" name="tipo_estudio" type="hidden">
    <div class="selector-tipo_estudio">   
      <select style="width:60%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;">
        <option value="">Seleccionar</option>
        <option value="0">Pnf</option>
        <option value="1">Tradicional</option>
        <option value="2">Prosecución</option>                                                            
      </select>      
    </div>
  </div>
</div>






<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="fe_gr_alu">Fecha de Graduación</label>  
    <input id="fe_gr_alu" name="fe_gr_alu" type="text" placeholder="Fe Gr Alu" class="form-control" value="<?php echo $fe_gr_alu;?>" style="background: white">
  </div>
</div>






</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_graduacion_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
  </div>
</div>

     </div>
    </fieldset>
  </form>
</div>

<script>
  $(document).ready(function(){

      $('.selector-tipo_estudio select').change(function(){          
              $('#tipo_estudio').val($(this).val());       
       });

  });
</script>

</body>
</html>


