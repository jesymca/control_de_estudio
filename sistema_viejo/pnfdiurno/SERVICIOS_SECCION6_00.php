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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION6_0.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione el Pensum <span class="badge" style="background:#F0F0F0;color: #0C4783;font-weight:bold">01</span></label>
				</div>
		
				<p>
					<form class="form-inline">
					<div class="col-sm-12">
							<div class="form-group" style="width: 200px;margin-left: 2%;margin-top:0px">								
									<label for="pensum">Pensum</label>    
									<input type="hidden" id="pensum" name="pensum">
									<div class="selector-pensum">   
										<select style="width:150px;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
									</div> 								
							</div>
					
					
							<div class="form-group" style="margin-left: 40%;margin-top:-15px">
								<div class="col-md-12">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente" style="background: #0C4783;width:120px"/> 
								    <a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;margin-left: 55%;margin-top:-60px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
					 </div>					 
					
					 </div>	
				</p>







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


