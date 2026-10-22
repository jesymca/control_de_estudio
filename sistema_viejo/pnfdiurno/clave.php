
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user_clave']==1) {
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
			max-width: 450px;
			min-width: 220px;
		}
		.col-md-12 {
			padding-left:30px; 
			padding-right:30px;
			min-width: 205px;
		}


	</style>

	<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
	<link rel="stylesheet" type="text/css" href="css/themes/icon.css">


	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.easyui.min.js"></script>



</head>
<body>
	<div class="form-group" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="clave_insert.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-cog"></span> Cambiar contraseña</label>
				</div>

				<br>


				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
						<label for="claveanterior">Clave anterior</label>  
						<input id="claveanterior" name="claveanterior" type="password" placeholder="Contraseña" class="form-control" required style="background-color: #F0F0F0">
					</div>
				</div>

                	<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
						<label for="clave">Nueva Clave</label>  
						<input id="clave" name="clave" type="password" placeholder="Contraseña" class="form-control" required style="background-color: #F0F0F0">
					</div>
				</div>



				<!-- Button -->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
						<input type="submit" class="btn btn-primary" name="submit" value="Cambiar" style="background: #0C4783;"/> 
						<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
					</div>
				</div>


			</fieldset>
		</form>
	</div>

	<script>
		$(document).ready(function(){     



$("#Guardar").click(function(){
   var codigo = $('#codigo').val();
   var cedula = $('#cedula').val();   

   $.post("alumno_insert.php",{accion: "Guardar",codigo:codigo,cedula:cedula},function(res){
alert(res);

 }); 
 });

		});
	</script>

</body>
</html>
