<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
} else {
	header("Location: index.html");
	exit;
}
$now = time();
if($now > $_SESSION['expire']) {
	session_destroy();
	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
	exit;
}
require('db.php');

$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$nota = $_POST['nota'];
$lapso = $_POST['lapso'];
$tiplap = $_POST['tiplap'];
$cod_doc = $_POST['cod_doc'];
$acu = $_POST['acu'];
$cod_usu=$_SESSION['username'];
$seccion=$_POST['seccion'];

$carrera = substr($_POST['cod_mat'], 0, 1);

$sql = "SELECT cedula,carrera FROM alumno WHERE cedula='".$codigo."' and carrera='".$carrera."'";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
	$pertenece="1";

}else{

	$pertenece="0";


	include 'menu.php';


	echo '<html>
	<head>

		<style>
			body

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
			<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS2.php">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> El alumno no es de la carrera </label>
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
} 


$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."' AND tiplap='".$tiplap."'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

	$materialapso="0";
	include 'menu.php';


	echo '<html>
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
			<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS2.php">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> Materia ya cargada</label>
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
} else{
	$materialapso="1";
}


if ($pertenece=="1" && $materialapso=="1") {


	$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera)
	VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera')";

	if ($conn->query($sql) === TRUE) {
		echo "registro creado";
	} else {
		echo $conn->error;
	}

	date_default_timezone_set('America/Caracas');
	$hora = strftime("%I:%M:%S %p\n");
	$fecha = date('d-m-Y');
	$accion="Guardar";
	$cod=$_SESSION['id'];
	$usuario=$_SESSION['username'];

	$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

	if ($conn->query($sql) === TRUE) { 
	} 

	$conn->close();

		include 'menu.php';


	echo '<html>
	<head>

		<style>
			body

   #marco
			{
				width:400px;
				min-width: 400px;

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
			<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS2.php">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Carga Exitosa</label>
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

}













?>
