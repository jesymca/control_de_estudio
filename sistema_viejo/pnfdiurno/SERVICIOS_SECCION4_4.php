<?php
include 'menu.php';
$pensum = $_POST['pensum'];
$grado = $_POST['grado'];
$trayecto = $_POST['trayecto'];
$lapso = $_POST['lapso'];
include('/Classes/class_api.php');
$x=new PDF();
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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION4_5.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione la Seccion <span class="badge" style="background:#F0F0F0;color: #0C4783;font-weight:bold">05</span></label>
				</div>

				<center><table >
					<tr>
						<td>
	
	                    <div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
									<label for="seccion">Seccion</label>    
									<input type="hidden" id="seccion" name="seccion">
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
									<input type="hidden" id="trayecto" name="trayecto" value="<?php echo $trayecto;?>">
									<input type="hidden" id="grado" name="grado" value="<?php echo $grado;?>">
									<input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">

									<div class="selector-seccion">   
										<select style="width:150px;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
										<?php
                                        $x->listado_de_secciones($pensum);
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
									<input type="submit" class="btn btn-primary" name="submit" value="Siguiente" style="width:120px"/> 
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
		
	// $.ajax({				
	// 		type: "POST",
	// 		url: "getseccion2f_0.php",				
	// 		success: function(response)
	// 		{
	// 			$('.selector-seccion select').html(response).fadeIn();
	// 		}
	// });

	$('.selector-seccion select').change(function(){
	var v = $(this).val(); 
	$('#seccion').val(v);    
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


