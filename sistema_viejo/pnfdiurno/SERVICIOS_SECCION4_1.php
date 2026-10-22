<?php
include 'menu.php';
$pensum = $_POST['pensum'];
?>
<html>
<head>
	<style>
		body    
		input[type = "text"]
		{
			background:#658DB3;  
			font-weight:bold; 
			color:#000000; 
		}

	</style>
</head>
<body>
	<div class="container" id="marco" style="width:45%">
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION4_2.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione el Grado <span class="badge" style="background:#F0F0F0;color: #0C4783;font-weight:bold">02</span></label>

				</div>

				<center><table >
					<tr>
						<td>
							<div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
									<label for="grado">Grado</label>
									<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">					
									<input id="grado" name="grado" type="hidden">							
									<div class="selector-grado">   
										<select style="width:150px;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
											<option value="">Seleccionar</option>
											<?php
											if($pensum=="GXC" or $pensum=="CXC"){
												?>
												<option value="T">Tsu</option>		
												<option value="L">Licenciado</option>
												<?php
											}else{
												?>
												<option value="T">Tsu</option>
												<option value="I">Ingeniero</option>
												<?php
											}                     						
											?>											
										</select>      
									</div>
								</div>								
							</div>  
						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 45px;margin-left: 0px">
									<input type="submit" id="submit" class="btn btn-primary" name="submit" id="submit" value="Siguiente" style="width:120px"/> 

								</div>
							</div>
						</td>

						<td></td> 
						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-left:10px;margin-top:45px">

									<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table>

				<table id="editable_table" class="table table-bordered table-striped"></table></center>
			</fieldset>
		</form>
	</div>

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


			$('#grado').val('');
			$('.selector-grado select').change(function(){
				var v = $(this).val(); 
				$('#grado').val(v);       
			});


		// Show loading message
		function show_loading_message(){
			$('#loading_container').show();
			$('#marco').hide();
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


