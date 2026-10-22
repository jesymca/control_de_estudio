
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas']==1) {
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
      #marco
    {      
      max-width: 350px;
      min-width: 170px;
    }

  </style>
</head>
<body>



 <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Materias a mostrar</label>
    </div>
 <br>

 <center><p>
        
            <a href="SERVICIOS_SECCION6.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-edit"></span> Todas</a>
            <a href="SERVICIOS2D.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-tasks"></span> Solo una</a> 
            
        
            
             
      </p></center>








  </form>
</div>
</fieldset>
</form>



</body>
</html>


