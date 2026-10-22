<?php
include 'menu.php';
?>

<html>
<head>
	<style>
		body

		#marco
		{
			width:600px;
			min-width: 600px;
			border: 10px solid rgba(230, 28, 34,1);

		}
		#titulo_formulario{    
			background:#E61C22;   
		}

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
		<form class="form-horizontal" id="effect2" method="post" action="principal.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> El alumno no esta Activo</label>
				</div>

				<center><table>
					<tr>

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

									<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

								</div>
							</div>
						</td>
						<td width="10"></td>
						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
									<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
								</div>
							</div>
						</td>

					</tr>
				</tr>
			</table></center>
		</form>
	</div>
</fieldset>
</form>
</body>
</html>