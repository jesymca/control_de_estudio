<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['eliminar_seccion']==1) {
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
$accion="Eliminar_secc";
$cod=$_SESSION['id'];
$usuariox=$_SESSION['username'];

require("db.php");
date_default_timezone_set('America/Caracas');
$fecha = date('d-m-Y');
$hora = strftime("%I:%M:%S %p\n");


$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$cod_doc = $_POST['cod_doc'];
$seccion = $_POST['seccion']; 


$carrera = substr($cod_mat, 0, 1);

$sql = "SELECT * FROM notas_copiar_materia WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'"; 
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {
while($fila = $resultado->fetch_assoc()) {   
    $codigo=$fila['codigo']; 
    $cod_mat=$fila['cod_mat']; 
    $nota=$fila['nota']; 
    $lapso=$fila['lapso']; 
    $tiplap=$fila['tiplap']; 
    $cod_doc=$fila['cod_doc']; 
    $cod_usu=$fila['cod_usu']; 
    $acu=$fila['acu']; 
    $hora=$fila['hora']; 
    $seccion=$fila['seccion']; 
    $electiva=$fila['electiva']; 
    
    $sql = "INSERT INTO notas_auditoria (accion,cod,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,hora,seccion,electiva,fecha) VALUES ('$accion','$cod','$usuariox','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$hora','$seccion','$electiva','$fecha')";
    $result = $conn->query($sql); 
  
  }

}
$pensum=substr($cod_mat, 0, 1)."X".substr($cod_mat, 4, 1);

$sql = "INSERT INTO agregarseccion_auditoria(accion,cod,usuario,pensum,cod_mat,seccion,cod_doc,lapso,aula,descrip,hora,fecha,tipo,electiva) VALUES ('$accion','$cod','$usuario','$pensum','$cod_mat','$seccion','$cod_doc','$lapso','$aula','$descrip','$hora','$fecha','$tipo','$electiva');";
$result = mysqli_query($conn, $sql);

$sql = "SELECT * FROM notas_copiar_materia WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
$sql = "DELETE FROM `notas_copiar_materia` WHERE cod_mat='".$cod_mat."' AND `lapso`='".$lapso."' AND `cod_doc`='".$cod_doc."' AND `seccion`='".$seccion."'"; 
$result = $conn->query($sql);

include 'menu.php';

echo '<html>
<head>

  <style>
    body

   #marco
    {
      width:400px;
      min-width: 400px;

    }
    input[type = "text"]
    {
      background:#658DB3;  
      font-weight:bold; 
      color:#000000; 
    }

  </style>
</head>
<body>
  <div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post" action="SERVICIOS6.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario"> 
          <label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Eliminacion Exitosa</label>
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

}else{

  include 'menu.php';


  echo '<html>
  <head>

    <style>
      body

  #marco
      {
        width:600px;
        min-width: 600px;
        border: 10px solid rgba(230, 28, 34,1);

      }
      #titulo_formulario{    
      background:#E61C22;   
    }
    input[type = "text"]
    {
      background:#658DB3;  
      font-weight:bold; 
      color:#000000; 
    }

  </style>
</head>
<body>
  <div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post" action="SERVICIOS6.php">
      <fieldset>
        <div class="form-group" id="titulo_formulario"> 
          <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> La Sección Esta vacía</label>
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


