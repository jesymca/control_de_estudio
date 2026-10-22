
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
		<form class="form-horizontal" id="effect2" method="post" action="comedor generador.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione el Lapso</label>
				</div>

				<center><table >
					<tr>
						<td>
							
							<div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
									<label for="lapso">Lapso</label>
								    <input type="hidden" id="lapso" name="lapso">							    
					      		</div>

						       <div class="selector-lapso">   
					                <select style="width:150px;height: 38px;margin-top:8px;margin-left: -185px;position: absolute;" required></select>   
				               </div>        
							</div>

						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 45px;margin-left: 0px">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente" style="background: #0C4783;width:120px"/> 
								</div>
							</div>
						</td>


						<td></td> 

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-left:10px;margin-top:45px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table>				

			</fieldset>
		</form>
	</div>





	<script type="text/javascript">
		$(document).ready(function() {
			
			buscarlapso();

			function buscarlapso(){
				var parametros = {                        		
					"opcion":"getlapso", 
					"campos":"lapso"      
				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getselect.php",        
					success: function(response)
					{             
						$('.selector-lapso select').html(response).fadeIn();
					}
				});
			}

	   	$(".selector-lapso select").change(function() {       
			var v = $(this).val();     
			$('#lapso').val(v); 
			
		}); 

			
		});
	</script>

</body>
</html>


