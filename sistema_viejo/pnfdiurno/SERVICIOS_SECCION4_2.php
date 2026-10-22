<?php
include 'menu.php';
$pensum = $_POST['pensum'];
$grado = $_POST['grado'];
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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION4_3.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione el Trayecto <span class="badge" style="background:#F0F0F0;color: #0C4783;font-weight:bold">03</span></label>
				</div>

				<center><table >
					<tr>
						<td>
							<div class="select_trayecto">
								<div class="form-group">
									<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
										<label for="trayecto">Trayecto</label> 
										<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
										<input type="hidden" id="grado" name="grado" value="<?php echo $grado;?>">
										<input id="trayecto" name="trayecto" type="hidden">
									<div class="selector-trayecto">   
										<select style="width:150px;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required>
										<option value="">Seleccionar</option>
	                    <?php
	                    if($grado=="T"){
                         echo'<option value="0">Trayecto 0</option>';
	                     echo'<option value="1">Trayecto 1</option>';
	                     echo'<option value="2">Trayecto 2</option>';
	                    }else{
	                     echo'<option value="0">Trayecto 0</option>';
	                     echo'<option value="3">Trayecto 3</option>';
	                     echo'<option value="4">Trayecto 4</option>';
	                    }
	                    ?>
	                                   
						
										</select>      
									</div> 
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
		
	$('.selector-trayecto select').change(function(){
	var v = $(this).val(); 
	$('#trayecto').val(v);    
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


