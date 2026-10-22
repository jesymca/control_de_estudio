
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
			max-width: 620px;
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

	</style>

	<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
	<link rel="stylesheet" type="text/css" href="css/themes/icon.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.easyui.min.js"></script>



</head>
<body>
	<div class="form-group" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="SECCION_ESCEL_ACTIVAR.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tags"></span> Activar Seccion</label>
				</div>

				<br>
				<div class="row">
					<div class="col-md-12">


						<p>					
							<div class="form-group">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="cod_doc">Docente</label>  
									<input id="cod_doc" name="cod_doc" type="text" class="form-control" required>
								</div>



								<div class="selector-docente" id="sp1">   
									<select style="width:50%;;height: 38px;margin-top:25px"></select>  
								</div>
							</div>
						</p>
						<p>
							<div class="form-group">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="cod_mat">Materia</label>  
									<input id="cod_mat" name="cod_mat" type="text" class="form-control" required>
								</div>


								<div class="selector-cod_mat" id="sp5">   
									<select style="width:50%;;height: 38px;margin-top:25px"></select>     
								</div>
							</div>
						</p>
						<p>

							<div class="form-group">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="lapso">lapso</label>  
									<input id="lapso" name="lapso" type="text" class="form-control" required>
								</div>

								<div class="selector-lapso" id="sp2">   
									<select style="width:50%;;height: 38px;margin-top:25px"></select>    
								</div>

							</div>

						</p>
						<p>
							<div class="form-group">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="seccion">Seccion</label>  
									<input id="seccion" name="seccion" type="text" class="form-control" required>
								</div>

								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px;padding-left:0px;">  
									<div class="selector-seccion" id="sp6">   
										<select style="width:100%;height: 38px;margin-top:25px;margin-left: 0%;"></select>     
									</div>
								</div>
							</div>
						</p>

						<p>
							<div class="form-group">
								<div class="col-md-12">
									<input type="submit" class="btn btn-primary" name="submit" value="Activar" style="background: #0C4783;width:120px"/> 
									<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
									
								</div>								
							</div>
						</p>



                    </fieldset>

					</form>
				</div>
			


		<script>
			$(document).ready(function(){  

				$('#cod_doc').val('');
				$('#cod_mat').val('');
				$('#lapso').val('');
				$('#seccion').val('');

				$.ajax({
					type: "POST",
					url: "getdocente_sec.php",
					success: function(response)
					{
						$('.selector-docente select').html(response).fadeIn();
					}
				});

				$('#sp1 select').click(function(){
					var v = $(this).val(); 
					$('#cod_doc').val(v); 
							
				}); 

				$('#sp5 select').click(function(){
					var v = $(this).val();     
					$('#cod_mat').val(v); 
					
				}); 


				$('#sp2 select').click(function(){
					var v = $(this).val();     
					$('#lapso').val(v); 
					buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
					
				}); 
				
				

				$(".selector-docente select").change(function() {
					$('#cod_mat').val('');
					$(".selector-cod_mat select").empty();
					$('#lapso').val('');
					$(".selector-lapso select").empty();					
					buscarmateria($(".selector-docente select").val());				

				}); 


				function buscarmateria(val1){
					var parametros = {
						"cod_doc":val1
						
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getseccion.php",				
						success: function(response)
						{
							$('.selector-cod_mat select').html(response).fadeIn();
						}
					});
				}



				$(".selector-cod_mat select").change(function() {				
					buscarlapso($(".selector-docente select").val(),$(".selector-cod_mat select").val())
					$('#lapso').val('');
					$(".selector-lapso select").empty();


				});		

				function buscarlapso(val1,val2){
					var parametros = {
						"cod_doc":val1,
						"cod_mat":val2,				
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getlapso_4.php",				
						success: function(response)
						{							
							$('.selector-lapso select').html(response).fadeIn();
						}
					});
				}


				$(".selector-lapso select").change(function() {				
					
					$('#seccion').val('');			
					$(".selector-seccion select").empty();
					buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
				});



				$('#sp6 select').click(function(){
					var v = $(this).val();     
					$('#seccion').val(v);					
					
				}); 
				

				function buscarseccion(val1,val2,val3){
					var parametros = {
						"cod_doc":val1,
						"cod_mat":val2,
						"lapso":val3
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getseccion2f.php",				
						success: function(response)
						{
							$('.selector-seccion select').html(response).fadeIn();
						}
					});
				}

			});
		</script>

	</body>
	</html>
