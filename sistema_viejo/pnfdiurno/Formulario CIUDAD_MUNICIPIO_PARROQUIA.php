
<?php

include 'menu.php';

$cedula = $_POST['codigo1'];
?>
<html>
<head>
	<style>
		#marco{
			width:90%;
			/*min-width: 880px;			
			max-width: 880px;*/			
		}
		#titulo_formulario{
			width:100%;
		}

	</style>
</head>
<body>

	<!--Formulario-->
	<div class="container" id="marco">
		<form class="form-horizontal" name="informacion" id="effect2" method="post" action="Formulario notas_insertc.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario">Ciudad Municipio Parroquia</label>
					<INPUT type="hidden" id="codigo1" name="codigo1" value="<?php echo $cedula;?>">	
					</div>
					<br />

					
					<table> 
						<tr> 
							<td style="width: 20px;"></td>

		                       <td style="width: 350px;">
								<div class="selector-ciudad">
									<div class="form-group">
										<div class="col-md-6" style="width: 170px;margin-top: 14px;">
											<label>Ciudad</label>
											<input id="ciudad" name="ciudad" type="text" class="form-control input-md" required style="background-color:#F0F0F0">
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top: -52px;margin-left: 150px">
											<select class="form-control" id="selectciudad" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>

							<td style="width: 10px;"></td>

							<td style="width: 350px;"> 
								<div class="selector-estado">
									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top:15px">
											<label for="estado">Estado</label>  
											<input id="estado" name="estado" type="text" class="form-control" required style="background-color:#F0F0F0;">
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top: -52px;margin-left: 150px">
											<select class="form-control" id="selectestado" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td> 

					
							

							<td style="width: 350px;"> 
								<div class="selector-municipios">
									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top:15px;margin-left: -3px">
											<label for="municipios">Municipios</label>  
											<input id="municipios" name="municipios" type="text" class="form-control" style="background-color:#F0F0F0;">
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top: -52px;margin-left: 150px">
											<select class="form-control" id="selectmunicipios" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>

							<td style="width: 10px;"></td>


							<td style="width: 350px;">
								<div class="selector-parroquias">
									<div class="form-group">
										<div class="col-md-6" style="width: 170px;margin-top: 14px;">
											<label>Parroquias</label>
											<input id="parroquias" name="parroquias" type="text" class="form-control input-md" required style="background-color:#F0F0F0;">
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12" style="width: 170px;margin-top: -52px;margin-left: 150px">
											<select class="form-control" id="selectparroquias" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>						
						</tr>
					</table>
				</form>
			</div>
		</fieldset>
	</form>
	<script src="js/jquery-3.1.1.min3.js"></script>
	<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {


		$.ajax({
			type: "POST",
			url: "getciudades.php",
			success: function(response)
			{
				$('.selector-ciudad select').html(response).fadeIn();
			}
		});

        $('#selectciudad').click(function(){
			buscarestado($(".selector-ciudad select").val());
			var v = $(this).val(); 
			var datos = v.split('|');
			$('#ciudad').val(datos[1]);
		});

		function buscarestado(val1){        
			var parametros = {
				"estado":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getestados.php",
				success: function(response)
				{
					$('.selector-estado select').html(response).fadeIn();
				}
			});        
		}

		
		 $('#selectestado').click(function(){
		 	buscarmunicipios($(".selector-estado select").val());
		 	var v = $(this).val();
		 	var datos = v.split('|');
			$('#estado').val(datos[1]);		 	
		 });




		function buscarmunicipios(val1){        
			var parametros = {
				"estado":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getmunicipios.php",
				success: function(response)
				{
					$('.selector-municipios select').html(response).fadeIn();
				}
			});        
		}

		$('#selectmunicipios').click(function(){
			buscarparroquias($(".selector-municipios select").val());
			var v = $(this).val(); 
            var datos = v.split('|');
			$('#municipios').val(datos[1]);			
		});


		function buscarparroquias(val1){  			
			var parametros = {
				"municipio":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getparroquias.php",
				success: function(response)
				{
					$('.selector-parroquias select').html(response).fadeIn();
				}
			});        
		}

		$('#selectparroquias').click(function(){			
			var v = $(this).val(); 
			var datos = v.split('|');
			$('#parroquias').val(datos[1]);				
		});


	});
</script>

</body>
</html>