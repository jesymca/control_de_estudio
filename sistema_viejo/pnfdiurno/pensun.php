
<?php

include 'menu.php';
?>
<html>
<head>

	<style>
		input[type = "text"]
		{
			background:#658DB3;  
			font-weight:bold; 
			color:#000000; 
		}

	</style>
</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="PENSA DE ESTUDIOS.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-paperclip"></span> Pensa de Estudios</label>
				</div>


				<center><table>
					<tr>
						<td width="100">		
							<!-- Text input-->

							<div class="form-group">
							<div class="col-md-12" style="width: 220px;margin-left: 20px;margin-top:-20px">
									<label for="pensum">Pensum</label>    
									<input type="hidden" id="pensum" name="pensum">
									<div class="selector-pensum" id="sp1">   
										<select style="width:90%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>      
									</div> 
								</div>
							</div>

						</td>
						<td width="20"></td>
						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-top:43px">
									<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="background: #0C4783;"/> 
								</div>
							</div>
						</td>

						<td width="10"></td>

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-top:43px">
									<a href="principal.php" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>


					</tr>
				</table></center>









			</form>
		</div>
	</fieldset>
</form>


<script>
	$(document).ready(function(){

		$.ajax({
			type: "POST",
			url: "getpensum.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

		$('#sp1 select').click(function(){
			var v = $(this).val(); 
			$('#pensum').val(v);   
		}); 

	});
</script>

</body>
</html>
