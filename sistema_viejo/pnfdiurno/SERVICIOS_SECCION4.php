<?php
include 'menu.php';
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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION4_1.php">
			<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione el Pensum <span class="badge" style="background:#F0F0F0;color: #0C4783;font-weight:bold">01</span></label>
			</div>
		
				<p>				
					
							
						    <div class="col-md-4" style="width:150px;margin-left: 0px">
									<label for="pensum">Pensum</label>    
									<input type="hidden" id="pensum" name="pensum">
									<div class="selector-pensum">   
										<select style="height: 38px;" required></select><!--  position: absolute; -->     
									</div> 								
							</div>
							
							
								<div class="col-md-4" style="width:120px;margin-top: 25px;margin-left: 70px">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente"/> 
								</div>
							
					
							
								<div class="col-md-4" style="width:120px;margin-top: 25px;margin-left: -20px">		
								    <a href="principal.php" class="btn btn-primary"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							
										 
					
					
				</p>


				<table id="editable_table" class="table table-bordered table-striped"></table></center>
			
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
			}); 
			
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


