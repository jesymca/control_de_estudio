
<?php
include 'menu_web.php';
?>

<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Login de Usuario</title>
	<link rel='stylesheet prefetch' href='css/bootstrap.css'>

	<style type="text/css">

		{		
			background:white;
			background-attachment: fixed;
			background-size:cover;
			background-repeat:no-repeat;
			background-position: center center;
		}

		#marco{
			width:70%;
			margin:10px auto;
			background:#FFFFFF;  
			border-radius:20px;      
			background: rgba(237, 237, 237,1);
			color:#000000;
			padding: 0;
			border-top-width: 4px;
			border-right-width: 4px;
			border-bottom-width: 4px;
			border-left-width: 4px;
			-webkit-box-shadow: 5px 5px 15px #575853;
			box-shadow: 5px 5px 15px #575853;
			border: 10px solid rgba(47, 164, 231,1);
			border-radius:20px; 
		}

		#titulo_formulario{
			width: 100% !important;
			background:#2FA4E7;
			text-align: center;
			font-size: 30px;
			border-top-left-radius:0px;
			border-top-right-radius:0px;
			color:#FFFFFF;
			margin:0px auto;    
		}

		input[type="text"]{
			background-color: #fff;
		}
		input[type="password"]{
			background-color: #fff;
		}

	</style>		

</style>

</head>
<body>

	<div class="container" id="marco" style="width:400px;margin-top:50px">
		<form class="form-horizontal" id="effect2" method="post" action="verificacion.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario">Login de Usuario</label>
				</div>

				<br>

				<center><table>

					<tr>          
						<div class="form-group">
							<div class="col-md-12" style="width: 100%;padding-left: 35px;padding-right:35px;">
								<input type="text" id="usuario" class="form-control" name="usuario" placeholder="Usuario" required autofocus=""/>
							</div>
						</tr>

						<tr>
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;padding-left: 35px;padding-right:35px;">
									<input type="password" id="clave" class="form-control" name="clave" placeholder="clave" required/>
								</div>
							</tr>

						</table></center>
						<center><table>
							<tr>
								<div class="form-group">
									<div class="col-md-12">
										<button type="submit" class="btn btn-lg btn-primary btn-block" style="width: 110px;background: #2FA4E7;">Aceptar</button>
									</div>
								</div>
							</tr>
						</table></center>
					</fieldset>
				</form>
			</div>
		</body>
		</html>
