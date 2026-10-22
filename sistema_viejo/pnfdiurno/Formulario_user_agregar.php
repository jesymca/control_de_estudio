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
require("db.php");
require("Security.php");
$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Agregar";
$id=$_POST["id"];
$nombre=$_POST["nombre"];
$login=$_POST["login"];
$clave=encriptar($_POST['clave']);
$alumno=$_POST["alumno"];
$docente=$_POST["docente"];
$notas=$_POST["notas"];
$notas_guardar=$_POST["notas_guardar"];
$notas_modificar=$_POST["notas_modificar"];
$notas_borrar=$_POST["notas_borrar"];
$lapso=$_POST["lapso"];
$lismat=$_POST["lismat"];
$seccion=$_POST["seccion"];
$tipos_lapso=$_POST["tipos_lapso"];
$nota=$_POST["nota"];
$user=$_POST["user"];
$user_clave=$_POST["user_clave"];
$auditoria=$_POST["auditoria"];
$actas=$_POST["actas"];
$historiales=$_POST["historiales"];
$agregar_seccion=$_POST["agregar_seccion"];
$inscribir_materia=$_POST["inscribir_materia"];
$copiar_seccion=$_POST["copiar_seccion"];
$eliminar_seccion=$_POST["eliminar_seccion"];
$horas=$_POST["horas"];
$aula=$_POST["aula"];
$electivas=$_POST["electivas"];
$cambiar_docente=$_POST["cambiar_docente"];
$cambiar_lapso=$_POST["cambiar_lapso"];
$cambiar_seccion=$_POST["cambiar_seccion"];
$cambiar_materia=$_POST["cambiar_materia"];
$desactivar_alumnos=$_POST["desactivar_alumnos"];

$sql = "INSERT INTO user(nombre,login,clave,alumno,docente,notas,notas_guardar,notas_modificar,notas_borrar,lapso,lismat,seccion,tipos_lapso,nota,user,user_clave,auditoria,actas,historiales,agregar_seccion,inscribir_materia,copiar_seccion,eliminar_seccion,horas,aula,electivas,cambiar_docente,cambiar_lapso,cambiar_seccion,cambiar_materia,desactivar_alumnos) VALUES ('$nombre','$login','$clave','$alumno','$docente','$notas','$notas_guardar','$notas_modificar','$notas_borrar','$lapso','$lismat','$seccion','$tipos_lapso','$nota','$user','$user_clave','$auditoria','$actas','$historiales','$agregar_seccion','$inscribir_materia','$copiar_seccion','$eliminar_seccion','$horas','$aula','$electivas','$cambiar_docente','$cambiar_lapso','$cambiar_seccion','$cambiar_materia','$desactivar_alumnos');";
if ($conn->query($sql) === TRUE) {
include 'menu.php';

echo '<html>
<head>

  <style>
 
   #marco
    {
      width:600px;
      min-width: 600px;

    }
  

  </style>
</head>
<body>
  <div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_user_lista.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario">
          <label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Se agrego el registro</label>
        </div>

        <center><table>
          <tr>

            <td width="100">
              <div class="form-group">
                <div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

                  <input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #0C4783;width:120px"/>

                </div>
              </div>
            </td>
            <td width="10"></td>
            <td width="100">
              <div class="form-group">
                <div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
                  <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
                </div>
              </div>
            </td>

          </tr>
        </tr>
      </table></center>
    </form>
  </div>
</fieldset>
</form>
</body>
</html>';

} else {
  include 'menu.php';
  echo '<html>
  <head>

    <style>

      #marco
      {
        width:600px;
        min-width: 600px;
        border: 10px solid rgba(230, 28, 34,1);

      }
      #titulo_formulario{
      background:#E61C22;
    }
  

  </style>
</head>
<body>
  <div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_user_lista.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario"> 
          <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> No se pudo agregar el registro</label>
        </div>

        <center><table>
          <tr>

            <td width="100">
              <div class="form-group">
                <div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

                  <input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

                </div>
              </div>
            </td>
            <td width="10"></td>
            <td width="100">
              <div class="form-group">
                <div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
                  <a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
                </div>
              </div>
            </td>

          </tr>
        </tr>
      </table></center>
    </form>
  </div>
</fieldset>
</form>
</body>
</html>';
}

$conn->close();
?>
