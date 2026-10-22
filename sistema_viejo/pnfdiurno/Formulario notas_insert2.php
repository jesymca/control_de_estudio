<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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
$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$lapso = $_POST['lapso'];
$tiplap = $_POST['tiplap'];
$cod_doc = $_POST['cod_doc'];
$seccion = $_POST['seccion'];
$carrera=substr($cod_mat, 0, 1);
$nota = "0";
$acu = 0;
$cod_usu=$_SESSION['username'];

$sql = "SELECT cedula FROM alumno WHERE cedula='".$codigo."'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {


$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

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
 <form class="form-horizontal" id="effect2" method="post" action="Formulario_inscribirmateria_lista.php">
  <fieldset>
 <div class="form-group" id="titulo_formulario"> 
    <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> Materia ya cargada</label>
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


$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera)
VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera')";

if ($conn->query($sql) === TRUE) {
 echo "registro creado";
} else {
 echo $conn->error;
}

date_default_timezone_set('America/Caracas');
$hora = strftime("%I:%M:%S %p\n");
$fecha = date('d-m-Y');
$accion="Guardar";
$cod=$_SESSION['id'];
$usuario=$_SESSION['username'];


$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

if ($conn->query($sql) === TRUE) { 
} 

$conn->close();

header("Location: Formulario_inscribirmateria_lista.php");
} 


}else{


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
 <form class="form-horizontal" id="effect2" method="post" action="Formulario_inscribirmateria_lista.php">
  <fieldset>
 <div class="form-group" id="titulo_formulario"> 
    <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> El alumno no existe</label>
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
}



?>
