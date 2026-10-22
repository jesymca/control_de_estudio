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
require("db.php");
$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Agregar";
$id=$_POST["id"];
$cedula=$_POST["cedula"];
$codigo=$_POST["cedula"];
$nombre=$_POST["nombre"];
$carrera=$_POST["carrera"];
$mencion=$_POST["mencion"];
$plan=$_POST["plan"];
$actividad=$_POST["actividad"];
$sexo=$_POST["sexo"];
$edocivil=$_POST["edocivil"];
$fechanac=$_POST["fechanac"];
$edad=$_POST["edad"];
$direccion=$_POST["direccion"];
$telefonoh=$_POST["telefonoh"];
$telefonoc=$_POST["telefonoc"];
$telefonot=$_POST["telefonot"];
$email=$_POST["email"];
$ingreso=$_POST["ingreso"];
$turno=$_POST["turno"];

$sql = "INSERT INTO alumno_auditoria(accion,cod,usuario,codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,ingreso,semestre,egreso,pasantia,turno,trabajo,beca,ireceptor,folio,tomo,rusnies,discapacid,pnf,trayecto,hora,fecha) VALUES ('$accion','$cod','$usuario','$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$lugar','$municipio','$estado','$procedenci','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$tipingreso','$ingreso','$semestre','$egreso','$pasantia','$turno','$trabajo','$beca','$ireceptor','$folio','$tomo','$rusnies','$discapacid','$pnf','$trayecto','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

$sql = "INSERT INTO alumno(codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,ingreso,turno) VALUES ('$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$ingreso','$turno');";
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_alumno_lista.php">
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_alumno_lista.php">
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
