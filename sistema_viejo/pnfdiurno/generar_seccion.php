<?php
include 'menu.php';
?>
<html>
<head>
	<style>
		#marco{
			width:800px;
			min-width: 800px;		
			max-width: 800px;		
		}
		#titulo_formulario{
			width:100%;
		}

	</style>
</head>
<body>
	<center><table>
		<tr>
			<td>


				<!--Formulario-->
				<div class="container" id="marco">
					<form class="form-horizontal" name="informacion" id="effect2" method="post" action="">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario">Generar seccion</label>
								
							</div>
							<br />

							<table> 
								<tr>
									<td style="width: 20px;"></td>
									<td style="width: 250px;">
										<div class="form-group">
											<div class="col-md-12">
												<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="background: #0C4783;width:120px"/> 
												<a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
											</div>
										</div>
									</td>

								</tr>
							</table> 


							<table> 
								<tr> 
									<td style="width: 35px;"></td>
									<td style="width: 70px;">
										<div class="form-group">
											<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: 0px">
												<label for="pensum">Pensum</label>    
												<input type="hidden" id="pensum" name="pensum">
												<div class="selector-pensum">   
													<select style="width:120px;height: 38px;" required></select>  
												</div> 
											</div>
										</div>

									</td>
									<td style="width: 20px;"></td>
									<td style="width: 70px;"> 

										<div class="form-group">
											<div class="col-md-12" style="width:100%;margin-top:0px;margin-left: 0px">
												<label for="cod_mat">Codigo Anterior</label>    
												<input type="hidden" id="cod_mat" name="cod_mat">
												<div class="selector-cod_mat">   
													<select style="width:120px;height: 38px;" required></select>  
												</div> 
											</div>
										</div>			


									</td> 
									<td style="width: 20px;"></td>
									<td style="width: 100px;">

										<div class="form-group">
											<div class="col-md-12" style="width:100%;margin-top:0px;margin-left: 0px">
												<label for="cod_mat2">Codigo Nuevo</label>    
												<input type="hidden" id="cod_mat2" name="cod_mat2">
												<div class="selector-cod_mat2">   
													<select style="width:120px;height: 38px;" required></select>  
												</div> 
											</div>
										</div>	

									</td>

								</tr>
							</table>
						</fieldset>
					</form>
				</div>


			</td>
		</table></center>	


		<noscript id="noscript_container">
			<div id="noscript" class="error">
				<p>JavaScript support is needed to use this page.</p>
			</div>
		</noscript>

		<div class="container" id="loading_container" style="width: 400px">
			<form class="form-horizontal">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario"><span class="glyphicon glyphicon-time"></span> Informacion</label>
					</div>
					<div id="loading_container2">
						<center><h4>Cargando por favor espere...</h4></center>
					</div>
				</fieldset>
			</form>
		</div>



		<script type="text/javascript">
			$(document).ready(function() {
				hide_loading_message();

				function buscarpensum(){                        
					var parametros = {                        
						"opcion":"pensum"
					}

					$.ajax({
						data:valor1,
						type: "POST",
						url: "getselect.php",
						success: function(response)
						{
							$('.selector-pensum select').html(response).fadeIn();
						}
					});
				}

				$('.selector-pensum select').change(function(){
					var v = $(this).val(); 
					$('#pensum').val(v);     
				}); 

				buscarmateria($('#pensum').val());

				function buscarmateria(val1){
					var parametros = {
						"pensum":val1,                       		
						"opcion":"getmateria_ext"
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getselect.php",        
						success: function(response)
						{
							$('.selector-cod_mat select').html(response).fadeIn();
						}
					});
				}



					  // Show loading message
					  function show_loading_message(){
					  	$('#marco').hide();					  	
					  	$('#loading_container').show();
					  }
                      // Hide loading message
                      function hide_loading_message(){
                      	$('#loading_container').hide();
                      	$('#marco').show();  	                   
                      }
                  });
              </script>

          </body>
          </html>		