<?php
// session_start();
// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
// } else {
// 	header("Location: index.html");
// 	exit;
// }
// $now = time();
// if($now > $_SESSION['expire']) {
// 	session_destroy();
// 	echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
// 	exit;
// }




$F=$_POST['cod_mat'];// $cod_mat


for ($i=0;$i<count($F);$i++) 
	
{
	if ($_POST['alumno']<>"") {	
		//guarda_seccion($_POST['alumno'],$_POST['cod_doc'],$F[$i],$_POST['lapso'],$_POST['seccion'],$_SESSION['username']);
		
echo $_POST['alumno']." - ".$F[$i]." - ".$_POST['cod_doc']." - ".$_POST['lapso']." - ".$_POST['seccion']."<br>";

		$Carga="true";
	}   
}


// function guarda_seccion($alumno,$cod_doc1,$cod_mat1,$lapso1,$seccion1,$cod_usu1) {
	
// 	require('db.php');
// 	//include('validacion.php');

// 	$cod_mat = $cod_mat1;
// 	$nota = "0";
// 	$acu = 0;
// 	$lapso = $lapso1;

// 	if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
// 		$tiplap=substr($cod_mat, 1, 1);
// 	}else{
// 		$tiplap="";
// 	}

// 	$codigo =  $alumno;
// 	$cod_doc = $cod_doc1;
// 	$cod_usu=$cod_usu1;
// 	$seccion=$seccion1;
// 	$carrera = substr($cod_mat, 0, 1);





// 	$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."'";

// 	$result = $conn->query($sql);

// 	if ($result->num_rows > 0) {

// 		$materialapso="SI";
// 		include 'menu.php';


// 		echo '<html>
// 		<head>

// 			<style>          
				
// 			#marco{
// 				width:600px;
// 				min-width: 600px;
// 				border: 10px solid rgba(230, 28, 34,1);
// 			}
			
//            #titulo_formulario{
// 			background:#E61C22;
// 		}

// 		input[type = "text"]
// 		{
// 			background:#658DB3;  
// 			font-weight:bold; 
// 			color:#000000; 
// 		}

// 	</style>
// </head>
// <body>
// 	<div class="container" id="marco">
// 		<form class="form-horizontal" id="effect2" method="post" action="Formulario_cargar_materias_form2.php">
// 			<fieldset>
// 				<div class="form-group" id="titulo_formulario"> 
// 					<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> Materia ya cargada</label>
// 				</div>

// 				<center><table>
// 					<tr>

// 						<td width="100">
// 							<div class="form-group">
// 								<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 									<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 								</div>
// 							</div>
// 						</td>
// 						<td width="10"></td>
// 						<td width="100">
// 							<div class="form-group">
// 								<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 									<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
// 								</div>
// 							</div>
// 						</td>

// 					</tr>
// 				</tr>
// 			</table></center>
// 		</form>
// 	</div>
// </fieldset>
// </form>
// </body>
// </html>';
// } else{
// 	$materialapso="NO";
// }


// if ($materialapso=="NO") {


// 	//if(validar_alumno1($codigo)=="true" and validar_cod_mat1($cod_mat)=="true" and validar_lapso1($lapso)=="true"){
// 	//if(validar_alumno1($codigo)=="true"){



// 	$sql = "SELECT * FROM notas WHERE codigo='".$codigo."' AND cod_mat='".$cod_mat."' AND lapso='".$lapso."'";

// 	$result = $conn->query($sql);

// 	if ($result->num_rows == 0) {



// 		date_default_timezone_set('America/Caracas');
// 		$hora = strftime("%I:%M:%S %p\n");
// 		$fecha = date('d-m-Y');
		

// 		$sql = "INSERT INTO notas(codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu,seccion,carrera,fecha)
// 		VALUES ('$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu','$seccion','$carrera','$fecha')";

// 		if ($conn->query($sql) === TRUE) {
// 			//echo "Se cargo el alumno: ".$codigo."<br>";
// 		} else {
// 			echo "No se pudo crear el registro";
// 		}

// 		$accion="Guardar_inc_va";
// 		$cod=$_SESSION['id'];
// 		$usuario=$_SESSION['username'];
		


// 		$sql = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,cod_usu,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$cod_usu','$acu')";

// 		if ($conn->query($sql) === TRUE) { 
// 		} 



// 		$sql = "SELECT * FROM `lapso` WHERE carrera= '".$carrera."' ORDER BY `lapso` ASC";
// 		$resultado = $conn->query($sql);
// 		if ($resultado->num_rows > 0) {
// 			while($fila = $resultado->fetch_assoc()) {
// 				$lapso_actual= $fila['lapso'];

// 			}  
// 		}

// 		if($lapso==$lapso_actual){
// 			$sql = "UPDATE alumno SET  actividad = '1' WHERE cedula ='".$codigo."'";
// 			$conn->query($sql);
// 		}
		

// 		$conn->close();



// 	}
// //}//FIN VALIDACION


// }
// }


// if ($Carga=="true") {

// 	include 'menu.php';

// 	echo '<html>
// 	<head>
// 		<style>			
//             #marco
// 			{
// 				width:500px;
// 				min-width: 400px;
// 			}
// 			input[type = "text"]
// 			{
// 				background:#658DB3;  
// 				font-weight:bold; 
// 				color:#000000; 
// 			}

// 		</style>
// 	</head>
// 	<body>
// 		<div class="container" id="marco">
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_cargar_materias_form2.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Se cargo con exito</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #0C4783;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
// 									</div>
// 								</div>
// 							</td>

// 						</tr>
// 					</tr>
// 				</table></center>
// 			</form>
// 		</div>
// 	</fieldset>
// </form>
// </body>
// </html>';
// }

?>
