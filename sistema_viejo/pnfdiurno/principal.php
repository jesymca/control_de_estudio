
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
      <label id="titulo_formulario">Control de Estudio</label>
    </div>


  <center><div class="row">
    <div class="col-md-12">

      
      <p>
           <a href="nominas.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-book"></span> Actas de calificacion</a>
           <a href="SERVICIOS.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-print"></span> Historial academico</a>
           <a href="SERVICIOS0.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-th-list"></span> Notas por alumno</a>              
      </p>


      <p style="margin-top: -10px">
            <a href="Formulario_agregarseccion_form_agregar.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-floppy-disk"></span> Crear Seccion</a> 
            <a href="SERVICIOS_x1.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-tasks"></span> Inscribir materia</a> 
            <a href="Formulario_notas2_tabla_index.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-stats"></span> Notas por seccion</a> 
            
            
             
      </p>

        <p style="margin-top: -10px">  
            
            <a href="CONDUCTA_FROM.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-random"></span> Conducta</a> 
            <a href="CULMINACION_FROM.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-road"></span> Culminacion</a> 
            <a href="RANGO_FROM.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-map-marker"></span> Ubicación y Rango</a>                                                                     
      </p>

     
      
      <p style="margin-top: -10px">
            <a href="copiar_seccion_1.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-copy"></span> Copiar Seccion</a> 
            <a href="CERTIFICACION_FROM.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-menu-hamburger"></span> Certificacion de Notas</a>                                                          
            <a href="CONSTANCIA_FROM.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-subtitles"></span> Constancia de estudio</a> 
      </p>

      <p style="margin-top: -10px">
        <a href="Formulario_docente_lista.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-lock"></span> Expediente del Docente</a>
            <a href="Formulario_alumno_lista.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-book"></span> Expediente del Alumno</a>
            <a href="Formulario alumno.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-barcode"></span> Inscripcion</a>                  
      </p>


      <p style="margin-top: -10px">
        <a href="PENSA.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-paperclip"></span> Pensa de Estudios</a>
            
            <a href="auditorias.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-signal"></span> Auditoría</a>
            <a href="configuraciones.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-wrench"></span> Configuración</a>  
      </p>

      <p style="margin-top: -10px">
            
            <a href="cambiar carrera.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-retweet"></span> Cambiar Carrera</a>  
            <a href="activar alumno.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-list-alt"></span> Activar alumno</a>        
            <a href="certificacion_gtu.php" class="btn btn-primary" style="margin-top: 10px"><span class="glyphicon glyphicon-link"></span> Certificacion GTU</a>
      </p>

    </div>
  </div></center>


<!-- 
    <a href="PENSA.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-paperclip"></span> Pensa de Estudios</a>
    <a href="SERVICIOS2.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-th-list"></span> Cargar Notas</a>
    <a href="auditorias.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-signal"></span> Auditoría</a>
    <a href="configuraciones.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-wrench"></span> Configuración</a>


  <div class="btn-group btn-group-justified">
   <a href="nominas.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-book"></span> Actas de calificacion</a>
   <a href="SERVICIOS.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-print"></span> Historial academico</a>
   <a href="activar alumno.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-list-alt"></span> Activar alumno</a>

            <a href="Formulario_docente_lista.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-lock"></span> Expediente del Docente</a>
            <a href="Formulario_alumno_lista.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-book"></span> Expediente del Alumno</a>
            <a href="Formulario alumno.php" class="btn btn-primary" style=""><span class="glyphicon glyphicon-barcode"></span> Inscripcion</a>
 -->


        </fieldset>
        </form>   
      </div>

</body>
</html>
