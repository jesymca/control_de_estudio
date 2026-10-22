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


require('db.php');
include('/Classes/class_api.php');
$x=new PDF();

$codigo = strtoupper($_POST['cedula']);
$cedula = strtoupper($_POST['cedula']);
$nombre = strtoupper($_POST['nombre']);
$grado = $_POST['grado'];
$carrera = strtoupper(substr($_POST['carrera'], 0, 1));
$pensum = $x->pensum($carrera);
$mencion = substr($pensum, 1, 1);
$plan = substr($pensum, 2, 1);

$actividad = 1;
$sexo = $_POST['sexo'];
$edocivil = strtoupper($_POST['edocivil']);
$lugar = strtoupper($_POST['lugar']);
$municipio = strtoupper($_POST['municipio']);
$estado = strtoupper($_POST['estado']);
$procedenci = strtoupper($_POST['procedenci']);
$fechanac = $_POST['fechanac'];
$edad = $_POST['edad'];
$direccion = strtoupper($_POST['direccion']);
$telefonoh = $_POST['telefonoh'];
$telefonoc = $_POST['telefonoc'];
$telefonot = $_POST['telefonot'];
$email = $_POST['email'];
$tipingreso = strtoupper($_POST['tipingreso']);
$ingreso = $_POST['ingreso'];
$semestre = $_POST['semestre'];
$egreso = $_POST['egreso'];
$pasantia = $_POST['pasantia'];
$turno = $_POST['turno'];
$trabajo = $_POST['trabajo'];
$beca = $_POST['beca'];
$ireceptor = strtoupper($_POST['ireceptor']);
$folio = $_POST['folio'];
$tomo = $_POST['tomo'];
$rusnies = $_POST['rusnies'];
$discapacid = $_POST['discapacid'];
$pnf = $_POST['pnf'];
$trayecto = $_POST['trayecto'];
$fcedula = $_POST['fcedula'];
$inscripmilt = $_POST['inscripmilt'];
$ftitulo = $_POST['ftitulo'];
$fcerfidicado = $_POST['fcerfidicado'];
$fnotas = $_POST['fnotas'];
$fdosfotos = $_POST['fdosfotos'];
$fpinscrip = $_POST['fpinscrip'];
$fnacimie = $_POST['fnacimie'];


 // echo "cedula= ".$cedula."<BR>";
 // echo "nombre= ".$nombre."<BR>";
 // echo "grado= ".$grado."<BR>";
// echo "carrera= ".$carrera."<BR>";
// echo "mencion= ".$mencion."<BR>";
// echo "plan= ".$plan."<BR>";
// echo "actividad= ".$actividad."<BR>";
// echo "sexo= ".$sexo."<BR>";
// echo "edocivil= ".$edocivil."<BR>";
// echo "lugar= ".$lugar."<BR>";
// echo "municipio= ".$municipio."<BR>";
// echo "estado= ".$estado."<BR>";
// echo "procedenci= ".$procedenci."<BR>";
// echo "fechanac= ".$fechanac."<BR>";
// echo "edad= ".$edad."<BR>";
// echo "direccion= ".$direccion."<BR>";
// echo "telefonoh= ".$telefonoh."<BR>";
// echo "telefonoc= ".$telefonoc."<BR>";
// echo "telefonot= ".$telefonot."<BR>";
// echo "email= ".$email."<BR>";
// echo "tipingreso= ".$tipingreso."<BR>";
// echo "ingreso= ".$ingreso."<BR>";
// echo "semestre= ".$semestre."<BR>";
// echo "egreso= ".$egreso."<BR>";
// echo "pasantia= ".$pasantia."<BR>";
// echo "turno= ".$turno."<BR>";
// echo "trabajo= ".$trabajo."<BR>";
// echo "beca= ".$beca."<BR>";
// echo "ireceptor= ".$ireceptor."<BR>";
// echo "folio= ".$folio."<BR>";
// echo "tomo= ".$tomo."<BR>";
// echo "rusnies= ".$rusnies."<BR>";
// echo "discapacid= ".$discapacid."<BR>";
// echo "pnf= ".$pnf."<BR>";
// echo "trayecto= ".$trayecto."<BR>";
// echo "fcedula= ".$fcedula."<BR>";
// echo "inscripmilt= ".$inscripmilt."<BR>";
// echo "ftitulo= ".$ftitulo."<BR>";
// echo "fcerfidicado= ".$fcerfidicado."<BR>";
// echo "fnotas= ".$fnotas."<BR>";
// echo "fdosfotos= ".$fdosfotos."<BR>";
// echo "fpinscrip= ".$fpinscrip."<BR>";
// echo "fnacimie= ".$fnacimie."<BR>";


$sql = "INSERT INTO alumno(codigo,cedula,nombre,carrera,mencion,plan,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,ingreso,semestre,egreso,pasantia,turno,trabajo,beca,ireceptor,folio,tomo,rusnies,discapacid,pnf,trayecto,fcedula,inscripmilt,ftitulo,fcerfidicado,fnotas,fdosfotos,fpinscrip,fnacimie,grado)
VALUES ('$codigo','$cedula','$nombre','$carrera','$mencion','$plan','$actividad','$sexo','$edocivil','$lugar','$municipio','$estado','$procedenci','$fechanac','$edad','$direccion','$telefonoh','$telefonoc','$telefonot','$email','$tipingreso','$ingreso','$semestre','$egreso','$pasantia','$turno','$trabajo','$beca','$ireceptor','$folio','$tomo','$rusnies','$discapacid','$pnf','$trayecto','$fcedula','$inscripmilt','$ftitulo','$fcerfidicado','$fnotas','$fdosfotos','$fpinscrip','$fnacimie','$grado')";

if ($conn->query($sql) === TRUE) {
 
header("Location: alumnoinscrito.php");
 
} else {
 include 'menu.php';
 echo '
 <html>
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
 <form class="form-horizontal" id="effect2" method="post" action="Formulario alumno.php">
  <fieldset>
 <div class="form-group" id="titulo_formulario"> 
    <label id="titulo_formulario"><span class="glyphicon glyphicon-remove-sign"></span> '.$conn->error.'</label>
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

$conn->close();

?>
