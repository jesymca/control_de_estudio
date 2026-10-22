
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
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
  <style>
      #marco
    {      
      max-width: 650px;
      min-width: 250px;
    }

  </style>


 <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario">Certificacion GTU</label>
    </div>

  <center><div class="row">
    <div class="col-md-12">    

      <p style="margin-top: 10px">            
            <a href="CONDUCTA_FROM2.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-random"></span> Conducta</a> 
            <a href="CULMINACION_FROM2.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-road"></span> Culminacion</a> 
            <a href="RANGO_FROM2.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-map-marker"></span> Ubicación y Rango</a>                                                                     
      </p>     
      
      <p style="margin-top: -10px">            
            <a href="CERTIFICACION_FROM3.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-menu-hamburger"></span> Certificacion de Prosecucion</a>             
            <a href="CERTIFICACION_FROM2.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-menu-hamburger"></span> Certificacion de Notas</a> 
            <a href="principal.php" class="btn btn-primary" style="width: 100px;;margin-top: 10px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
      </p>     

    </div>
  </div></center>
         </fieldset>
        </form>   
      </div>

</body>
</html>
