<?php

session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
} else {
	 header("Location: index.html");
	 exit;
}
$now = time();
if($now > $_SESSION['expire']) {
	 session_destroy();
	 //echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
	 exit;
}

require("db.php");
require ("Security.php");

$username = $_SESSION['username'];
$claveanterior = $_POST['claveanterior'];

// echo "Clave usuario: ".$username."<br>";
// echo "Clave anterior: ".$claveanterior."<br>";

$sql = "SELECT * FROM user WHERE login='".$username."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = mysqli_fetch_array($result))
	{                      
		$id=$row['id'];
		$login=$row['login'];
		$claveguardada=$row["clave"];  
	}
}

// echo "Clave 1: ".$claveguardada."<br>";
// echo "Clave 2: ".encriptar($claveanterior)."<br>";

if($claveguardada==encriptar($claveanterior) and $login==$username){  
	$sql = "UPDATE user SET  login='".$username."' , clave = '".encriptar($_POST['clave'])."' WHERE id ='".$id."'";
	$result = $conn->query($sql);
	
	include 'menu.php';


	echo '<html>
	<head>

		<style>		

            #marco
			{
				width:600px;
				min-width: 600px;
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
						<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> La contraseña se cambio con exito</label>
					</div>

					<center><table>
						<tr>

							<td width="100">
								<div class="form-group">
									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #0C4783;width:120px"/> 

									</div>
								</div>
							</td>
							<td width="10"></td>
							<td width="100">
								<div class="form-group">
									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
										<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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
</html>';
}else{

	include 'menu.php';


	echo '<html>
	<head>

		<style>		

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
						<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> No se pudo cambiar la contraseña</label>
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
</html>';

}

?>
