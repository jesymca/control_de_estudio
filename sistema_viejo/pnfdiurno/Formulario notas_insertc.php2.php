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
include('validacion.php');

$codigo = $_POST['codigo'];
$cod_mat = $_POST['cod_mat'];
$electiva = $_POST['electiva'];
$nota = $_POST['nota'];

if ($nota == ""){
	$nota="0";
}

$lapso = $_POST['lapso'];

if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
	$tiplap=substr($cod_mat, 1, 1);
}else{
	$tiplap="";
}

$cod_doc = $_POST['cod_doc'];
$acu = $_POST['acu'];
$cod_usu=$_SESSION['username'];
$seccion=$_POST['seccion'];

$carrera = substr($_POST['cod_mat'], 0, 1);

$sql = "SELECT cedula,carrera FROM alumno WHERE cedula='".$codigo."' and carrera='".$carrera."'";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
	$pertenece="SI";

	while($fila = $resultado->fetch_assoc()) {  

		$ID=$fila['id'];	
	}

}else{

	$pertenece="NO";


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
		<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_index.php?id=$ID">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> El alumno no es de la carrera </label>
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

if ($pertenece=="SI") {
	$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."'";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		$materialapso="SI";
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
			<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_index.php?id=$ID">
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
	$materialapso="NO";
}
}

if ($pertenece=="SI" && $materialapso=="NO") {

	if(validar_alumno2($codigo)=="true" and validar_cod_mat2($cod_mat)=="true" and validar_lapso2($lapso)=="true"){

		
		date_default_timezone_set('America/Caracas');
		$hora = strftime("%I:%M:%S %p\n");
		$fecha = date('d-m-Y');

		$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera,electiva,fecha)
		VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera','$electiva','$fecha')";

		$conn->query($sql);

		
		$accion="Guardar";
		$cod=$_SESSION['id'];
		$usuario=$_SESSION['username'];

		$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";
		$conn->query($sql);



		$sql = "SELECT * FROM `lapso` WHERE carrera= '".$carrera."' ORDER BY `lapso` ASC";
		$resultado = $conn->query($sql);
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {
				$lapso_actual= $fila['lapso'];

			}  
		}

		if($lapso==$lapso_actual){
			$sql = "UPDATE alumno SET  actividad = '1' WHERE cedula ='".$codigo."'";
			$conn->query($sql);
		}


		$conn->close();

		include 'menu.php';


		echo '<html>
		<head>

			<style>
				
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
				<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_index.php?id=$ID">
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
}









?>
