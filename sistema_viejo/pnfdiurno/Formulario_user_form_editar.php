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
$nombre = $row['nombre'];
$login = $row['login'];
$clave = desencriptar($row['clave']);
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_user_edit.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-pencil"></span> Editar user</label>
            </div>
<p>
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
    <input id="id1" name="id1" type="hidden" class="form-control" value="<?php echo $id;?>">
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
    <label for="login">Login</label>  
    <input id="login" name="login" type="text" placeholder="Login" class="form-control" value="<?php echo $login;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="clave">Clave</label>
    <input id="clave" name="clave" type="password" placeholder="Clave" class="form-control" value="<?php echo $clave;?>" style="background:#F0F0F0">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="alumno">Alumno</label>  
    <input id="alumno" name="alumno" type="text" placeholder="Alumno" class="form-control" value="<?php echo $alumno;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="docente">Docente</label>  
    <input id="docente" name="docente" type="text" placeholder="Docente" class="form-control" value="<?php echo $docente;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="notas">Notas</label>  
    <input id="notas" name="notas" type="text" placeholder="Notas" class="form-control" value="<?php echo $notas;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="notas_guardar">Notas Guardar</label>  
    <input id="notas_guardar" name="notas_guardar" type="text" placeholder="Notas Guardar" class="form-control" value="<?php echo $notas_guardar;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="notas_modificar">Notas Modificar</label>  
    <input id="notas_modificar" name="notas_modificar" type="text" placeholder="Notas Modificar" class="form-control" value="<?php echo $notas_modificar;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="notas_borrar">Notas Borrar</label>  
    <input id="notas_borrar" name="notas_borrar" type="text" placeholder="Notas Borrar" class="form-control" value="<?php echo $notas_borrar;?>">
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
    <label for="lismat">Lismat</label>  
    <input id="lismat" name="lismat" type="text" placeholder="Lismat" class="form-control" value="<?php echo $lismat;?>">
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
    <label for="tipos_lapso">Tipos Lapso</label>  
    <input id="tipos_lapso" name="tipos_lapso" type="text" placeholder="Tipos Lapso" class="form-control" value="<?php echo $tipos_lapso;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="nota">Nota</label>  
    <input id="nota" name="nota" type="text" placeholder="Nota" class="form-control" value="<?php echo $nota;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="user">User</label>  
    <input id="user" name="user" type="text" placeholder="User" class="form-control" value="<?php echo $user;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="user_clave">User Clave</label>  
    <input id="user_clave" name="user_clave" type="text" placeholder="User Clave" class="form-control" value="<?php echo $user_clave;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="auditoria">Auditoria</label>  
    <input id="auditoria" name="auditoria" type="text" placeholder="Auditoria" class="form-control" value="<?php echo $auditoria;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="actas">Actas</label>  
    <input id="actas" name="actas" type="text" placeholder="Actas" class="form-control" value="<?php echo $actas;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="historiales">Historiales</label>  
    <input id="historiales" name="historiales" type="text" placeholder="Historiales" class="form-control" value="<?php echo $historiales;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="agregar_seccion">Agregar Seccion</label>  
    <input id="agregar_seccion" name="agregar_seccion" type="text" placeholder="Agregar Seccion" class="form-control" value="<?php echo $agregar_seccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="inscribir_materia">Inscribir Materia</label>  
    <input id="inscribir_materia" name="inscribir_materia" type="text" placeholder="Inscribir Materia" class="form-control" value="<?php echo $inscribir_materia;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="copiar_seccion">Copiar Seccion</label>  
    <input id="copiar_seccion" name="copiar_seccion" type="text" placeholder="Copiar Seccion" class="form-control" value="<?php echo $copiar_seccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="eliminar_seccion">Eliminar Seccion</label>  
    <input id="eliminar_seccion" name="eliminar_seccion" type="text" placeholder="Eliminar Seccion" class="form-control" value="<?php echo $eliminar_seccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="horas">Horas</label>  
    <input id="horas" name="horas" type="text" placeholder="Horas" class="form-control" value="<?php echo $horas;?>">
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
    <label for="electivas">Electivas</label>  
    <input id="electivas" name="electivas" type="text" placeholder="Electivas" class="form-control" value="<?php echo $electivas;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cambiar_docente">Cambiar Docente</label>  
    <input id="cambiar_docente" name="cambiar_docente" type="text" placeholder="Cambiar Docente" class="form-control" value="<?php echo $cambiar_docente;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cambiar_lapso">Cambiar Lapso</label>  
    <input id="cambiar_lapso" name="cambiar_lapso" type="text" placeholder="Cambiar Lapso" class="form-control" value="<?php echo $cambiar_lapso;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cambiar_seccion">Cambiar Seccion</label>  
    <input id="cambiar_seccion" name="cambiar_seccion" type="text" placeholder="Cambiar Seccion" class="form-control" value="<?php echo $cambiar_seccion;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="cambiar_materia">Cambiar Materia</label>  
    <input id="cambiar_materia" name="cambiar_materia" type="text" placeholder="Cambiar Materia" class="form-control" value="<?php echo $cambiar_materia;?>">
  </div>
</div>


<!-- Text input-->
<div class="form-group" style="padding-left: 15px;padding-right:20px">
  <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
    <label for="desactivar_alumnos">Desactivar Alumnos</label>  
    <input id="desactivar_alumnos" name="desactivar_alumnos" type="text" placeholder="Desactivar Alumnos" class="form-control" value="<?php echo $desactivar_alumnos;?>">
  </div>
</div>


</p>


<div class="form-group" style="padding-left: 15px">
  <div class="col-md-12">

    <input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
    <a href="Formulario_user_lista.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Volver</a>
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


