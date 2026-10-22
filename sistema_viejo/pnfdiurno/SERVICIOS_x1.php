
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
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

include 'menu.php';
?>
<html>
<head>
  <style>
    body    

    input[type = "text"]
    {
      background:#658DB3;  
      font-weight:bold; 
      color:#000000; 
    }

  </style>
</head>
<body>
 <div class="container" id="marco" style="width:40%">
   <form class="form-horizontal" id="effect2" method="post" action="">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-print"></span> Tipo de Inscripción</label>
    </div>

<br>
          
          <center><div class="form-group">
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
               <a href="SERVICIOS2b.php" class="btn btn-primary"><span class="glyphicon glyphicon-tasks"></span> Inscribir una materia a un alumno</a>
            </div>
          </div></center>

          <center><div class="form-group">
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
               <a href="SERVICIOS_SECCION.php" class="btn btn-primary"><span class="glyphicon glyphicon-sort-by-alphabet"></span> Inscribir una materia a varios alumnos</a>
            </div>
          </div></center>

            <center><div class="form-group">
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
               <a href="SERVICIOS2BC.php" class="btn btn-primary"><span class="glyphicon glyphicon-menu-hamburger"></span> Inscribir varias materias a un alumno</a>
            </div>
          </div></center>

         
            <center><div class="form-group"><!-- SERVICIOS7.php -->
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
               <a href="SERVICIOS_SECCION4.php" class="btn btn-primary"><span class="fa-magic fa"></span> Inscribir todas las materia a un alumno</a>
            </div>
          </div></center>  

</form>
</div>
</fieldset>
</form>

</body>
</html>


        