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
require("db.php");
$usuariox=$_SESSION['username'];
$cod=$_SESSION['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Agregar";
$id=$_POST["id"];
$cod_doc=$_POST["cod_doc"];
$cedula=$_POST["cedula"];
$nombre=$_POST["nombre"];
$condicion=$_POST["condicion"];
$depart=$_POST["depart"];
$sexo=$_POST["sexo"];
$fechanac=$_POST["fechanac"];
$titulo_c=$_POST["titulo_c"];
$titulo_l=$_POST["titulo_l"];
$tipo=$_POST["tipo"];
$ingreso=$_POST["ingreso"];
$categoria=$_POST["categoria"];
$dedicacion=$_POST["dedicacion"];
$telefono=$_POST["telefono"];
$asignatura=$_POST["asignatura"];
$horas_ad=$_POST["horas_ad"];
$horas_do=$_POST["horas_do"];
$observa=$_POST["observa"];
$actividad=$_POST["actividad"];
$turno=$_POST["turno"];

$sql = "INSERT INTO docente_auditoria(accion,cod,usuario,cod_doc,cedula,nombre,condicion,depart,sexo,fechanac,titulo_c,titulo_l,tipo,ingreso,categoria,dedicacion,telefono,asignatura,horas_ad,horas_do,observa,actividad,turno,hora,fecha) VALUES ('$accion','$cod','$usuariox','$cod_doc','$cedula','$nombre','$condicion','$depart','$sexo','$fechanac','$titulo_c','$titulo_l','$tipo','$ingreso','$categoria','$dedicacion','$telefono','$asignatura','$horas_ad','$horas_do','$observa','$actividad','$turno','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "INSERT INTO docente(cod_doc,cedula,nombre,condicion,depart,sexo,fechanac,titulo_c,titulo_l,tipo,ingreso,categoria,dedicacion,telefono,asignatura,horas_ad,horas_do,observa,actividad,turno) VALUES ('$cod_doc','$cedula','$nombre','$condicion','$depart','$sexo','$fechanac','$titulo_c','$titulo_l','$tipo','$ingreso','$categoria','$dedicacion','$telefono','$asignatura','$horas_ad','$horas_do','$observa','$actividad','$turno');";
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_docente_lista.php">
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_docente_lista.php">
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
