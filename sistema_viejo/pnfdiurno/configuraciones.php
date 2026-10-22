
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
<html>
<head>

  <style>
    #marco
    {      
      max-width: 550px;
      min-width: 230px;
    }

  </style>

</head>
<body>
 <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-wrench"></span> Configuraciones</label>
    </div>

    <br>

    <center><div class="row">
      <div class="col-md-12">
      
        <p>
          <a href="Formulario_lapso_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-calendar"></span> Lapsos</a>
          <!-- <a href="Formulario_tipos_lapso_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-gift"></span> Tipos de lapso</a> -->
          <a href="Formulario_lismat_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-hdd"></span> Pensum</a>
        </p>
    
    

        <p>
          <a href="Formulario_seccion_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-list"></span> Numero de Secciones</a>
          <a href="Formulario_user_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-user"></span> Usuarios</a>
          <a href="Formulario_aula_lista.php"  class="btn btn-primary"><span class="glyphicon glyphicon-blackboard"></span> Aula</a>
          
          </p>

         <p>

         <a href="Formulario_horas_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-time"></span> Hora</a>
         <a href="Formulario_directivos_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-screenshot"></span> Directivos</a>
         <a href="Formulario_pensum_lista.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-equalizer"></span> Carreras</a>

      

         <a href="principal.php" class="btn btn-primary"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
         </p>

        </div>
     </div></center>


  </form>
</div>
</fieldset>
</form>
</body>
</html>
