<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['lismat']==1) {
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
require('db.php');
$usuario=$_SESSION['username'];
$cod=$_SESSION['id'];
date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Modificar";
$id=$_POST["id"];
$pensum=$_POST["pensum"];
$cod_mat=$_POST["cod_mat"];
$descrip2=$_POST["descrip2"];
$creditos=$_POST["creditos"];
$aprobatori=$_POST["aprobatori"];
$semestre=$_POST["semestre"];
$trayecto=$_POST["trayecto"];
$divicion=$_POST["divicion"];
$nota=$_POST["nota"];
$cod_mat_libro_rector=$_POST["cod_mat_libro_rector"];
$cod_mat_ant=$_POST["cod_mat_ant"];

$sql = "INSERT INTO lismat_auditoria(accion,cod,usuario,pensum,cod_comp,cod_mat,descrip2,creditos,aprobatori,hora,fecha) VALUES ('$accion','$cod','$usuario','$pensum','$cod_comp','$cod_mat','$descrip2','$creditos','$aprobatori','$hora','$fecha');";
$result = mysqli_query($conn, $sql);

require('db.php');
$sql = "UPDATE lismat SET  pensum = '$pensum' , cod_mat = '$cod_mat' , descrip2 = '$descrip2' , creditos = '$creditos' , aprobatori = '$aprobatori' , semestre = '$semestre' , trayecto = '$trayecto' , divicion = '$divicion' , nota = '$nota' , cod_mat_libro_rector = '$cod_mat_libro_rector' , cod_mat_ant = '$cod_mat_ant' WHERE id ='".$id."'";
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_lismat_lista.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario">
          <label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Se actualizo el registro</label>
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
    <form class="form-horizontal" id="effect2" method="post" action="Formulario_lismat_lista.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario"> 
          <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> No se pudo actualizar el registro</label>
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
