<?php


function validar_pensum($pensum){

include('db.php');

$sql = "SELECT pensum FROM lismat WHERE pensum='".$pensum."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

 include('/Classes/class_api.php');
 $pdf=new PDF();

 $this->mensaje_color( "Formulario_agregarseccion_lista.php", "principal.php", "Pensun no valido", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
			


			
//             #marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Pensun no valido</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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
return "false";
$conn->close();
}
}

function validar_cod_mat($cod_mat){

include('db.php');

$sql = "SELECT cod_mat FROM lismat WHERE cod_mat='".$cod_mat."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

$this->mensaje_color( "Formulario_agregarseccion_lista.php", "principal.php", "Materia no valida", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
			

//             #marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Materia no valida</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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
return "false";
}
}



function validar_seccion($seccion){


include('db.php');

$sql = "SELECT seccion FROM seccion WHERE seccion='".$seccion."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_agregarseccion_lista.php", "principal.php", "Seccion no valida", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
// 			#marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Seccion no valida</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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

return "false";
}
}

function validar_cod_doc($cod_doc){

include('db.php');

$sql = "SELECT $cod_doc FROM docente WHERE cod_doc='".$cod_doc."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_agregarseccion_lista.php", "principal.php", "Docente no valido", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
// 			#marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Docente no valido</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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
return "false";
}
}

function validar_lapso($lapso){

include('db.php');

$sql = "SELECT lapso FROM lapso WHERE lapso='".$lapso."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_agregarseccion_lista.php", "principal.php", "Lapso no valido", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
// 			#marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_agregarseccion_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Lapso no valido</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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

return "false";
}
}

/////////////////////////////1//////////////////////////////////////////


function validar_pensum1($pensum){

include('db.php');

$sql = "SELECT pensum FROM lismat WHERE pensum='".$pensum."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Pensun no valido", 1, 1 );
// 	echo '<html>
// 	<head>

// 		<style>
			


			
//             #marco
// 			{
// 				width:400px;
// 				min-width: 400px;
//                 border: 10px solid rgba(230, 28, 34,1);
// 			}
// 			#titulo_formulario{    
//              background:#E61C22;   
//            }
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
// 			<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_lista.php">
// 				<fieldset>
// 					<div class="form-group" id="titulo_formulario"> 
// 						<label id="titulo_formulario"><span class="glyphicon glyphicon-ok"></span> Pensun no valido</label>
// 					</div>

// 					<center><table>
// 						<tr>

// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">

// 										<input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #E61C22;width:120px"/> 

// 									</div>
// 								</div>
// 							</td>
// 							<td width="10"></td>
// 							<td width="100">
// 								<div class="form-group">
// 									<div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
// 										<a href="principal.php" class="btn btn-primary" style="background: #E61C22;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
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
return "false";
}
}

function validar_cod_mat1($cod_mat){

include('db.php');

$sql = "SELECT cod_mat FROM lismat WHERE cod_mat='".$cod_mat."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Materia no valida", 1, 1 );

return "false";
}
}



function validar_seccion1($seccion){


include('db.php');

$sql = "SELECT seccion FROM seccion WHERE seccion='".$seccion."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Seccion no valida", 1, 1 );


return "false";
}
}

function validar_cod_doc1($cod_doc){

include('db.php');

$sql = "SELECT $cod_doc FROM docente WHERE cod_doc='".$cod_doc."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Docente no valido", 1, 1 );

return "false";
}
}

function validar_lapso1($lapso){

include('db.php');

$sql = "SELECT lapso FROM lapso WHERE lapso='".$lapso."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Lapso no valido", 1, 1 );

return "false";
}
}

function validar_tipo_lapso1($tipo){

include('db.php');

$sql = "SELECT tiplap FROM tipos_lapso WHERE tiplap='".$tipo."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";

}else{

	return "false";
}
}



function validar_alumno1($cedula){ 

	include('db.php');

	$sql = "SELECT cedula FROM alumno WHERE cedula='".$cedula."'"; 
	$resultado = $conn->query($sql); 
	if ($resultado->num_rows > 0) { 

	return "true";
    
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario_notas_lista.php", "principal.php", "Cedula no valida", 1, 1 );

	return "false";
}
}



//////////////////////////////////2//////////////////////////////

function validar_cod_mat2($cod_mat){

include('db.php');

$sql = "SELECT cod_mat FROM lismat WHERE cod_mat='".$cod_mat."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario notas_form_agregar.php", "principal.php", "Materia no valida", 1, 1 );

return "false";
}
}



function validar_seccion2($seccion){


include('db.php');

$sql = "SELECT seccion FROM seccion WHERE seccion='".$seccion."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario notas_form_agregar.php", "principal.php", "Seccion no valida", 1, 1 );

}
}

function validar_cod_doc2($cod_doc){

include('db.php');

$sql = "SELECT $cod_doc FROM docente WHERE cod_doc='".$cod_doc."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario notas_form_agregar.php", "principal.php", "Docente no valido", 1, 1 );

return "false";
}
}

function validar_lapso2($lapso){

include('db.php');

$sql = "SELECT lapso FROM lapso WHERE lapso='".$lapso."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  
	
	return "true";
	
}else{

	include 'menu.php';

    $this->mensaje_color( "Formulario notas_form_agregar.php", "principal.php", "Lapso no valido", 1, 1 );


return "false";
}
}

function validar_alumno2($cedula){

include('db.php');

$sql = "SELECT cedula FROM alumno WHERE cedula='".$cedula."'"; 
$resultado = $conn->query($sql); 

if ($resultado->num_rows > 0) {  

	return "true";
    
}else{

		include 'menu.php';

    $this->mensaje_color( "Formulario notas_form_agregar.php", "principal.php", "Cedula no valida", 1, 1 );

	return "false";
}
}

//////////////////////////////////3//////////////////////////////




?>

