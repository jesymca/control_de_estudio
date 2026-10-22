<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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
include 'db.php';
include 'menu.php';
$id = intval($_GET['id']);

$sql = "SELECT * FROM alumno WHERE id='" . $id . "'";
$resultado = $conn->query($sql);
while($fila = $resultado->fetch_assoc()) {
	$id=$fila["id"];
	$cedula=$fila["cedula"];
	$carrera=$fila["carrera"];
}

$conn->close();
$usuario=$_SESSION['username'];
include('/Classes/class_api.php');
$con=new PDF();
$pensum = $con->pensum($carrera);


?>
<html>
<head>
	<style>
		#marco{
			width:470px;
			min-width: 470px;		
			max-width: 470px;	
		}
		#titulo_formulario{
			width:100%;
		}

	</style>
</head>
<body>
	<center><table>
		<tr>
			<td>				
				<div class="container" id="marco">                                                       
					<form class="form-horizontal" name="informacion" id="effect2" method="post" action="edit2.php">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario">Cambiar Carrera</label>								
								</div>
								<br />
								<table> 
									<tr>
										<td style="width: 10px;"></td>
										<td style="width: 250px;">
											<div class="form-group">
												<div class="col-md-12">
													<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="width:120px"/> 
													<a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
												</div>
											</div>
										</td>
									</tr>
								</table> 
								<table> 
									<tr>
										<td style="width: 10px;"></td> 
										<td style="width: 100px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="cedula">Cedula</label>													
													<input type="text" id="cedula" class="form-control" name="cedula" style="background: #FFFFFF;" value="<?php echo $cedula?>" readonly required>					
												</div>												
											</div>
										</td> 
										<td style="width: 10px;"></td> 
										<td style="width: 370px;"> 
											<div class="form-group">
												<div class="col-md-12" style="width:70px;margin-top:0px;margin-left: 0px">
													<label for="carrera">Carrera</label>													
													<input type="text" id="carrera" class="form-control" name="carrera" style="background: #FFFFFF;" value="<?php echo $carrera?>" readonly required>					
												</div>
												<div class="selector-pensum">   
													<select style="width:250px;height: 37px;margin-top:28px;margin-left: 0px"></select> 
												</div>
											</div>
										</td>						
										</tr>
								</table>
							</fieldset>
						</form>
					</div>
				</td>
			</table></center>	
			

				<noscript id="noscript_container">
					<div id="noscript" class="error">
						<p>JavaScript support is needed to use this page.</p>
					</div>
				</noscript>

				<div class="container" id="loading_container" style="width: 400px">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario"><span class="glyphicon glyphicon-time"></span> Informacion</label>
							</div>
							<div id="loading_container2">
								<center><h4>Cargando por favor espere...</h4></center>
							</div>
						</fieldset>
					</form>
				</div>


<script type="text/javascript">
$(document).ready(function() {
hide_loading_message();

  $.ajax({
      type: "POST",
      url: "getpensum.php",
      success: function(response)
      {
        $('.selector-pensum select').html(response).fadeIn();
      }
    });

 $(".selector-pensum select").change(function() {
  var inicio = $(this).val();
  var v = inicio.substring(0,1);     
  $('#carrera').val(v);    
 }); 





















function show_loading_message(){
	$('#marco').hide();					  	
	$('#loading_container').show();
}

function hide_loading_message(){
	$('#loading_container').hide();
	$('#marco').show();  	                   
}

});
</script>

</body>
</html>		