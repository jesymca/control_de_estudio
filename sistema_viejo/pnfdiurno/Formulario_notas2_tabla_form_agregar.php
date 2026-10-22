
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
 <form class="form-horizontal" id="effect2" method="post" action="Formulario_notas2_tabla_index.php">
   <fieldset>
 <div class="form-group" id="titulo_formulario"> 
       <label id="titulo_formulario">Formulario Notas2</label>
</div>
            <br />
            <br />
            <div class="form-group" align="right">
                <div class="col-md-12">
                    <input type="submit" id="Guardar" class="btn btn-default" name="submit" value="Guardar" />
                    <a href="Formulario_notas2_tabla_index.php" class="btn btn-default">Salir</a>
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
          <label for="cedula">Cedula</label>  
          <input id="cedula" name="cedula" type="text" placeholder="Cedula" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="nombre">Nombre</label>  
          <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="nota">Nota</label>  
          <input id="nota" name="nota" type="text" placeholder="Nota" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="acu">Acu</label>  
          <input id="acu" name="acu" type="text" placeholder="Acu" class="form-control">
   </div>
 </div>


 </form>
 </div>
   </fieldset>
  </form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/Formulario_notas2.js"></script>


<script type="text/javascript">
$(document).ready(function() {

 $("#Guardar").click(function(){
   var cedula = $('#cedula').val();
   var nombre = $('#nombre').val();
   var nota = $('#nota').val();
   var acu = $('#acu').val();
   $.post("Formulario_notas2_tabla_insert.php",{accion: "Guardar",cedula:cedula,nombre:nombre,nota:nota,acu:acu},function(res){
 alert(res);
 $("#Nuevo").click()
 }); 
 });

 $("#Nuevo").click(function(){

   document.getElementById('id').value = "";
   document.getElementById('cedula').value = "";
   document.getElementById('nombre').value = "";
   document.getElementById('nota').value = "";
   document.getElementById('acu').value = "";
 });
 });

</script>
</body>
</html>
