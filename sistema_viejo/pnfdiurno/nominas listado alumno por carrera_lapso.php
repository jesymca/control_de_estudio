
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
			max-width: 600px;
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
		<form class="form-horizontal" id="effect2" method="post" action="LISTADO DE ALUMNOS CARRERA PDF2.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="fa-list-ul fa"></span> Listado de Alumnos por Carrera</label>
				</div>

				<br>
				<div class="row">
					<div class="col-md-12">

						<p>					
							<div class="form-group">
								<div class="col-md-12" style="width: 60%;margin-left: 2px;margin-top:0px">
									<label for="pensum">Pensum</label>    
									<input type="hidden" id="pensum" name="pensum">
									<div class="selector-pensum">   
										<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
									</div> 
								</div>
							</div>					
						</p>	

                       <br>
						<p>					
							<div class="form-group">
								<div class="col-md-12" style="width: 60%;margin-left: 2px;margin-top:0px">
									<label for="lapso">Lapso</label>    
									<input type="hidden" id="lapso" name="lapso">
									<div class="selector-lapso">   
										<select style="width:80%;height: 38px;margin-top:0px;margin-left: -30px;" required></select>      
									</div> 
								</div>
							</div>					
						</p>			
						<br><br>
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

			<form class="form-horizontal" method="post" action="LISTADO DE ALUMNOS CARRERA_LAPSO.php">		
				<input type="hidden" id="pensum2" name="pensum2">
				<input type="hidden" id="lapso2" name="lapso2">  

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

				$.ajax({
					type: "POST",
					url: "getpensum.php",
					success: function(response)
					{
						$('.selector-pensum select').html(response).fadeIn();
					}
				});
				$('.selector-pensum select').change(function(){
					var v = $(this).val(); 
					$('#pensum').val(v);
					$('#pensum2').val(v);
					buscarlapso($('#pensum').val());
				}); 
                
				$('.selector-lapso select').change(function(){
					var v = $(this).val(); 
					$('#lapso').val(v);
					$('#lapso2').val(v);
				});


				function buscarlapso(val1){					
				  var parametros = {	    
				    "pensum":val1        
				}
				$.ajax({
				    data:parametros,
				    type: "POST",
				    url: "getlapso_7.php",      
				    success: function(response)
				    {
				      $('.selector-lapso select').html(response).fadeIn();
				  }
				});
				} 


			});
		</script>
	</body>
	</html>
