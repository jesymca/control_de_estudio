
<?php
include 'menu.php';
$pensum=$_POST['pensum'];
$lapso=$_POST['lapso'];
$cedula=$_POST['cedula'];
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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION3_4.php">
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
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>"> 
					                <input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">
								    <input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>">			
								    <input type="hidden" id="seccion" name="seccion">		
								</div>

						       <div class="selector-seccion">   
					                <select style="width:150px;height: 38px;margin-top:8px;margin-left: -185px;position: absolute;" required></select>   
				               </div>        
							</div>

						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 45px;margin-left: 0px">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente" style="width:120px"/> 
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





	<script type="text/javascript">
		$(document).ready(function() {
			
		
        buscarseccion($('#pensum').val());

                        function buscarseccion(val1){
                        	var parametros = {
                        	    "pensum":val1,                        		
                        		"opcion":"getnumseccion" 
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{
                        			$('.selector-seccion select').html(response).fadeIn();
                        		}
                        	});
                        }
                        
                        $(".selector-seccion select").change(function() {       
                        	var v = $(this).val(); 
                        	$('#seccion').val(v);                         	
                        });

			
		});
	</script>

</body>
</html>


