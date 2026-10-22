
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
 <form class="form-horizontal" id="effect2" method="post">
	<fieldset>
 <div class="form-group" id="titulo_formulario"> 
		<label id="titulo_formulario">Formulario User</label>
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
		   <label for="nombre">Nombre</label>  
		   <input id="nombre" name="nombre" type="text" placeholder="Nombre" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
		   <label for="login">Login</label>  
		   <input id="login" name="login" type="text" placeholder="Login" class="form-control">
   </div>
 </div>


  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
		   <label for="clave">Clave</label>  
		   <input id="clave" name="clave" type="password" placeholder="Clave" class="form-control">
   </div>
 </div>


<!-- Button -->
<div class="form-group">
	<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
		<input type="Button" id="Nuevo" name="Nuevo" value="Nuevo" class="btn btn-default" />
		<input type="Button" id="Guardar" name="Guardar" value="Guardar" class="btn btn-default" />
		<input type="Button" id="Borrar" name="Borrar" value="Borrar" class="btn btn-default" />
		<input type="Button" id="Actualizar" name="Actualizar" value="Actualizar" class="btn btn-default" />
	</div>
 </div>
 </form>
 </div>
	</fieldset>
  </form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/Formulario user.js"></script>


<script type="text/javascript">
$(document).ready(function() {

 $("#Borrar").click(function(){
   if (confirm("Desea borrar el registro") == true) { 
   var valor = $('#id').val();
   $.post("Formulario user_delete.php",{accion: "Borrar", id:valor},function(res){
alert(res);
 $("#Nuevo").click();
 });
 } else {
 }
 });


$("#btnBuscar").click(function(){
var valor = $('#buscar').val();
obten_datos(valor);
});
 $("#Guardar").click(function(){
   var nombre = $('#nombre').val();
   var login = $('#login').val();
   var clave = $('#clave').val();
   $.post("Formulario user_insert.php",{accion: "Guardar",nombre:nombre,login:login,clave:clave},function(res){
alert(res);
 $("#Nuevo").click()
 }); 
 });


  $("#Actualizar").click(function(){
   var id = $('#id').val();
   var nombre = $('#nombre').val();
   var login = $('#login').val();
   var clave = $('#clave').val();
   $.post("Formulario user_update.php",{accion: "Actualizar",id:id,nombre:nombre,login:login,clave:clave},function(res){
alert(res);
 $("#Nuevo").click()
 }); 
 });

 $("#Nuevo").click(function(){

   document.getElementById('id').value = "";
   document.getElementById('nombre').value = "";
   document.getElementById('login').value = "";
   document.getElementById('clave').value = "";

 });
 });

</script>
</body>
</html>
