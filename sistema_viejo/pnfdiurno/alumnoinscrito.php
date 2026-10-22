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

include "db.php";


$id = intval($_GET['id']);


$sql = "UPDATE alumno SET  actividad = '0' WHERE id ='".$id."'";

if ($conn->query($sql) === TRUE) {
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
    <label id="titulo_formulario"><span class="glyphicon glyphicon-thumbs-up"></span> Alumno inscrito con éxito</label>
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
echo "error al desactivado alumno: ".$conn->error;
}




$conn->close();





?>



