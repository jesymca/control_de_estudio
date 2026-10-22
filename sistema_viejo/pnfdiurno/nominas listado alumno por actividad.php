
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
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
			max-width: 570px;
			min-width: 220px;
		}
		.col-md-12 {
			padding-left:30px; 
			padding-right:30px;
			min-width: 205px;
		}
		.selector-docente,.selector-cod_mat,.selector-lapso,.selector-tipolapso{
			padding-left:30px; 
			padding-right:30px;
			min-width: 350px;
		}
		.panel {
			overflow-y: hidden;
			overflow-x: hidden;
			-ms-overflow-style: hidden;
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
		<form class="form-horizontal" id="effect2" method="post" action="LISTADO DE ALUMNOS CARRERA PDF ACTIVO.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="fa-shield fa"></span> Listado de Alumnos Activos</label>
				</div>

				<br>
				<div class="row">
					<div class="col-md-12">						

						<p>
							<div class="form-group">
								<div class="col-md-12">
									<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="width:120px"/> 
						    		<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>				
							</div>
						</p>


					</form>
				</div>
			</fieldset>
		</form>

		<div class="form-group" style="padding-left:15px;padding-right:15px;">

			<form class="form-horizontal" method="post" action="LISTADO DE ALUMNOS ACTIVOS.php">
            <input type="hidden" id="carrera1" name="carrera1">
            <input type="hidden" id="lapso1" name="lapso1">
           
        
            <p>					
							<div class="form-group">
								<div class="col-md-12" style="width:50%;">
									<label for="url">Nombre de Archivo</label>  
									<input id="url" name="url" type="text" class="form-control" required value="Listado de alumnos">
								</div>							
							</div>	
	
			</p>

            <p> 
			
							<div class="form-group">
								<div class="col-md-12"> 
   								  <input type="submit" class="btn btn-primary" name="submit" value="Exportar a Excel" style="width:180px"/> 
							
						</p>

						        </div>
              </div>
		</form>
		
		</div>



		<script>
			$(document).ready(function(){ 	
			

			});
		</script>

	</body>
	</html>
