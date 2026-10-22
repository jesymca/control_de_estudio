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



if ($_POST['alumno_1']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_1'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_2']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_2'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_3']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_3'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_4']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_4'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_5']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_5'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_6']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_6'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_7']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_7'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_8']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_8'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_9']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_9'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_10']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_10'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_11']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_11'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_12']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_12'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_13']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_13'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_14']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_14'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_15']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_15'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_16']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_16'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_17']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_17'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_18']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_18'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_19']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_19'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_20']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_20'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_21']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_21'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_22']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_22'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_23']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_23'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_24']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_24'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_25']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_25'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_26']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_26'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_27']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_27'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_28']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_28'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_29']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_29'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_30']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_30'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_31']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_31'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_32']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_32'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_33']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_33'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_34']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_34'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_35']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_35'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_36']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_36'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_37']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_37'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_38']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_38'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_39']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_39'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}
if ($_POST['alumno_40']<>"") {	
	guarda_seccion($_POST['electiva'],$_POST['alumno_40'],$_POST['cod_doc'],$_POST['cod_mat'],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
}


function guarda_seccion($electiva,$alumno,$cod_doc1,$cod_mat1,$lapso1,$seccion1,$cod_usu1) {

	require('db.php');
//include('validacion.php');

	$cod_mat = $cod_mat1;

	$nota = "0";
	$acu = 0;
	$lapso = $lapso1;

	if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
		$tiplap=substr($cod_mat, 1, 1);
	}else{
		$tiplap="";
	}

	$codigo =  $alumno;
	$cod_doc = $cod_doc1;
	$cod_usu=$cod_usu1;
	$seccion=$seccion1;
	$carrera = substr($cod_mat, 0, 1);


	$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."'  AND seccion='".$seccion1."'";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		$materialapso="SI";
		include 'menu.php';


		echo '<html>
		<head>

			<style>          

#marco{
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
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION.php">
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


if ($materialapso=="NO") {


//if(validar_alumno1($codigo)=="true" and validar_cod_mat1($cod_mat)=="true" and validar_lapso1($lapso)=="true"){
//if(validar_alumno1($codigo)=="true"){



	$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."' AND seccion='".$seccion1."'";

	$result = $conn->query($sql);

	if ($result->num_rows == 0) {



		date_default_timezone_set('America/Caracas');
		$hora = strftime("%I:%M:%S %p\n");
		$fecha = date('d-m-Y');


		$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera,fecha,electiva)
		VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera','$fecha','$electiva')";

		$conn->query($sql);


		$accion="Guardar";
		$cod=$_SESSION['id'];
		$usuario=$_SESSION['username'];


		$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

		$conn->query($sql);

		$conn->close();

	}

}

}
if ($_POST['alumno_1']<>"") {
	include 'menu.php';

	echo '<html>
	<head>
		<style>			
             #marco
			{
				width:500px;
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
			<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION.php">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Se cargo los alumno con exito</label>
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
