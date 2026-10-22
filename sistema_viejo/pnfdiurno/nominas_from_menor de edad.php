
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
			max-width: 520px;
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
		<form class="form-horizontal" id="effect2" method="post" action="nominas_menor de edad.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tags"></span> Listado menores de edad</label>
				</div>

				<br>
				<div class="row">
					<div class="col-md-12">


						<p>					
							<div class="form-group">
								<div class="col-md-12" style="width:100px;">
									<label for="desde">Desde</label>  
									<input id="desde" name="desde" type="text" class="form-control" required>
								</div>
								
							</div>

							<div class="form-group">
								<div class="col-md-12" style="width:100px;margin-left: 0%;margin-top:0px">
									<label for="hasta">Hasta</label>  
									<input id="hasta" name="hasta" type="text" class="form-control" required>
								</div>								
							</div>							
						</p>
						

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

			<form class="form-horizontal" method="post" action="NOMINA DE ASISTENCIA ESCEL.php">
            <input type="hidden" id="cod_doc1" name="cod_doc1">
            <input type="hidden" id="cod_mat1" name="cod_mat1">
            <input type="hidden" id="lapso1" name="lapso1">
            <input type="hidden" id="seccion1" name="seccion1">
            <input type="hidden" id="tiplap1" name="tiplap1">
           
        
            <p>					
							<div class="form-group">
								<div class="col-md-12" style="width:50%;">
									<label for="url">Nombre de Archivo</label>  
									<input id="url" name="url" type="text" class="form-control" required value="Nomina asistencia">
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

                 $('#cod_mat').val('');
                 $('#cod_doc').val('');
                 $('#lapso').val('');
                 $('#seccion').val('');


				$.ajax({
					type: "POST",
					url: "getdocente.php",
					success: function(response)
					{
						$('.selector-docente select').html(response).fadeIn();
					}
				});

				$('#sp1 select').click(function(){
					var v = $(this).val(); 
					$('#cod_doc').val(v); 
					$('#cod_doc1').val(v); 			
				}); 

				$('#sp5 select').click(function(){
					var v = $(this).val();     
					$('#cod_mat').val(v); 
					$('#cod_mat1').val(v); 
				}); 


				$('#sp2 select').click(function(){
					var v = $(this).val();     
					$('#lapso').val(v); 
					buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
                    $('#lapso1').val(v); 
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
					$('#seccion1').val(v);
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
