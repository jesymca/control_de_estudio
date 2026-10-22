<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user']==1) {
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
require("Security.php");
$id = intval($_GET['id']);
$query = "SELECT * FROM user WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
  $id = $row['id'];
  $nombre = $row['nombre'];
  $login = $row['login'];
  $clave = encriptar($row['clave']);
  $alumno = $row['alumno'];
  $docente = $row['docente'];
  $notas = $row['notas'];
  $notas_guardar = $row['notas_guardar'];
  $notas_modificar = $row['notas_modificar'];
  $notas_borrar = $row['notas_borrar'];
  $lapso = $row['lapso'];
  $lismat = $row['lismat'];
  $seccion = $row['seccion'];
  $tipos_lapso = $row['tipos_lapso'];
  $nota = $row['nota'];
  $user = $row['user'];
  $user_clave = $row['user_clave'];
  $auditoria = $row['auditoria'];
  $actas = $row['actas'];
  $historiales = $row['historiales'];
  $agregar_seccion = $row['agregar_seccion'];
  $inscribir_materia = $row['inscribir_materia'];
  $copiar_seccion = $row['copiar_seccion'];
  $eliminar_seccion = $row['eliminar_seccion'];
  $horas = $row['horas'];
  $aula = $row['aula'];
  $electivas = $row['electivas'];
  $cambiar_docente = $row['cambiar_docente'];
  $cambiar_lapso = $row['cambiar_lapso'];
  $cambiar_seccion = $row['cambiar_seccion'];
  $cambiar_materia = $row['cambiar_materia'];
  $desactivar_alumnos = $row['desactivar_alumnos'];
}
$conn->close();
?>
<div class="form-group" id="marco" style="width:500px;">
  <form class="form-horizontal" id="effect2" method="post" action="Formulario_user_agregar.php">
    <fieldset>
      <div class="form-group" id="titulo_formulario">
        <label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar user</label>
      </div>
        <p>

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
    <label for="login">Login</label>  
    <input id="login" name="login" type="text" placeholder="Login" class="form-control">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="clave">Clave</label>  
    <input id="clave" name="clave" type="password" placeholder="Clave" class="form-control" style="background: #F0F0F0">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="alumno">Alumno</label>  
    <input id="alumno" name="alumno" type="text" placeholder="Alumno" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="docente">Docente</label>  
    <input id="docente" name="docente" type="text" placeholder="Docente" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="notas">Notas</label>  
    <input id="notas" name="notas" type="text" placeholder="Notas" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="notas_guardar">Notas Guardar</label>  
    <input id="notas_guardar" name="notas_guardar" type="text" placeholder="Notas Guardar" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="notas_modificar">Notas Modificar</label>  
    <input id="notas_modificar" name="notas_modificar" type="text" placeholder="Notas Modificar" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="notas_borrar">Notas Borrar</label>  
    <input id="notas_borrar" name="notas_borrar" type="text" placeholder="Notas Borrar" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="lapso">Lapso</label>  
    <input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="lismat">Lismat</label>  
    <input id="lismat" name="lismat" type="text" placeholder="Lismat" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="seccion">Seccion</label>  
    <input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="tipos_lapso">Tipos Lapso</label>  
    <input id="tipos_lapso" name="tipos_lapso" type="text" placeholder="Tipos Lapso" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="nota">Nota</label>  
    <input id="nota" name="nota" type="text" placeholder="Nota" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="user">User</label>  
    <input id="user" name="user" type="text" placeholder="User" class="form-control" value="0">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="user_clave">User Clave</label>  
    <input id="user_clave" name="user_clave" type="text" placeholder="User Clave" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="auditoria">Auditoria</label>  
    <input id="auditoria" name="auditoria" type="text" placeholder="Auditoria" class="form-control" value="0">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="actas">Actas</label>  
    <input id="actas" name="actas" type="text" placeholder="Actas" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="historiales">Historiales</label>  
    <input id="historiales" name="historiales" type="text" placeholder="Historiales" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="agregar_seccion">Agregar Seccion</label>  
    <input id="agregar_seccion" name="agregar_seccion" type="text" placeholder="Agregar Seccion" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="inscribir_materia">Inscribir Materia</label>  
    <input id="inscribir_materia" name="inscribir_materia" type="text" placeholder="Inscribir Materia" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="copiar_seccion">Copiar Seccion</label>  
    <input id="copiar_seccion" name="copiar_seccion" type="text" placeholder="Copiar Seccion" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="eliminar_seccion">Eliminar Seccion</label>  
    <input id="eliminar_seccion" name="eliminar_seccion" type="text" placeholder="Eliminar Seccion" class="form-control" value="0">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="horas">Horas</label>  
    <input id="horas" name="horas" type="text" placeholder="Horas" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="aula">Aula</label>  
    <input id="aula" name="aula" type="text" placeholder="Aula" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="electivas">Electivas</label>  
    <input id="electivas" name="electivas" type="text" placeholder="Electivas" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cambiar_docente">Cambiar Docente</label>  
    <input id="cambiar_docente" name="cambiar_docente" type="text" placeholder="Cambiar Docente" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cambiar_lapso">Cambiar Lapso</label>  
    <input id="cambiar_lapso" name="cambiar_lapso" type="text" placeholder="Cambiar Lapso" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cambiar_seccion">Cambiar Seccion</label>  
    <input id="cambiar_seccion" name="cambiar_seccion" type="text" placeholder="Cambiar Seccion" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="cambiar_materia">Cambiar Materia</label>  
    <input id="cambiar_materia" name="cambiar_materia" type="text" placeholder="Cambiar Materia" class="form-control" value="1">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <div class="col-md-12" style="width: 93%;margin-left: 20px;margin-top:0px">
    <label for="desactivar_alumnos">Desactivar Alumnos</label>  
    <input id="desactivar_alumnos" name="desactivar_alumnos" type="text" placeholder="Desactivar Alumnos" class="form-control" value="1">
  </div>
</div>

        </p>


<div class="form-group">
  <div class="col-md-12" style="margin-left: 20px;">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="width:120px"/> 
    <a href="Formulario_user_lista.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


