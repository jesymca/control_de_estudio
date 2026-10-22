
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


 <!--Formulario-->
 <div class="container" id="marco">
 <form class="form-horizontal" id="effect2" method="post" action="Formulario_inscribir_materia_tabla_index.php">
   <fieldset>
 <div class="form-group" id="titulo_formulario"> 
       <label id="titulo_formulario">Inscribir Materia</label>
</div>
            <br />
            <br />
            <div class="form-group" align="right">
                <div class="col-md-12">
                    <input type="submit" id="Guardar" class="btn btn-default" name="submit" value="Guardar" />
                    <a href="Formulario_inscribir_materia_tabla_index.php" class="btn btn-default">Salir</a>
                </div>
            </div>
  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="id">Id</label>  
          <input id="id" name="id" type="text" placeholder="Id" class="form-control" disabled="true">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="alumno">Alumno</label>  
          <input id="alumno" name="alumno" type="text" placeholder="Alumno" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="seccion">Seccion</label>  
          <input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control">
   </div>
 </div>




 </form>
 </div>
   </fieldset>
  </form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/Formulario_inscribir_materia.js"></script>


<script type="text/javascript">
$(document).ready(function() {

 $("#Guardar").click(function(){
   var alumno = $('#alumno').val();
   var seccion = $('#seccion').val();
   var descripcion = $('#descripcion').val();
   var creditos = $('#creditos').val();
   $.post("Formulario_inscribir_materia_tabla_insert.php",{accion: "Guardar",alumno:alumno,seccion:seccion,descripcion:descripcion,creditos:creditos},function(res){
 alert(res);
 $("#Nuevo").click()
 }); 
 });

 $("#Nuevo").click(function(){

   document.getElementById('id').value = "";
   document.getElementById('alumno').value = "";
   document.getElementById('seccion').value = "";
   document.getElementById('descripcion').value = "";
   document.getElementById('creditos').value = "";
 });
 });

</script>
</body>
</html>
