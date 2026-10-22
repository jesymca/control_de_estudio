
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
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
include 'menu.php';
$id = intval($_GET['id']);

include "db.php";
$sql = "SELECT * FROM notas WHERE id='".$id."'";        
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) { 
		$codigo=$fila["codigo"];
		$cod_mat=$fila["cod_mat"];
		$lapso=$fila["lapso"];
		$tiplap=$fila["tiplap"];
		$cod_doc=$fila["cod_doc"];
		$seccion=$fila["seccion"];
		$carrera=$fila["carrera"];              
	}
}

switch ($carrera)  {
      case "M":
      $pensum="MXC";
      break;
      case "T":
      $pensum="TXC";
      break;
      case "E":
      $pensum="EXC";
      break;
      case "I":
      $pensum="IXC";
      break;
      case "G":
      $pensum="GXC";
      break;   
      case "O":
      $pensum="OXC";
      break;
      case "A":
      $pensum="AXC";
      break;
    }

?>
<html>
<head>

	<style>
		#marco
		{      
			max-width: 620px;
			min-width: 220px;
		}
		.col-md-12 {
			padding-left:30px; 
			padding-right:30px;
			min-width: 205px;
		}
		.selector-docente,.selector-cod_mat,.selector-lapso,.selector-tipolapso{
			padding-left:30px; 
			padding-right:30px;
			min-width: 350px;
		}

	</style>

	<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
	<link rel="stylesheet" type="text/css" href="css/themes/icon.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.easyui.min.js"></script>



</head>
<body>
	<div class="form-group" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_edit.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-book"></span> Editar materia</label>
				</div>

				<br>
				<div class="row">
					<div class="col-md-12">


						<p>	

							<INPUT type="hidden" id="codigo1" name="codigo1" value="<?php echo $id;?>">									

								<div class="form-group">
									<div class="col-md-12" style="width: 0%;margin-left: 20px;margin-top:0px">
										
										<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width:50%;">
										<label for="codigo">Codigo</label>  
										<input id="codigo" name="codigo" type="text" class="form-control" value="<?php echo $codigo;?>" readonly style="background: white;">
									</div>
								</div>


								<div class="form-group">
									<div class="col-md-12" style="width:50%;">
										<label for="cod_doc">Docente</label>  
										<input id="cod_doc" name="cod_doc" type="text" class="form-control" required value="<?php echo $cod_doc;?>" readonly style="background: white;">
									</div>

									<div class="selector-docente" id="sp1">   
										<select style="width:50%;height: 38px;margin-top:23px"></select>      
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
										<label for="cod_mat">Materia</label>  
										<input id="cod_mat" name="cod_mat" type="text" class="form-control" required value="<?php echo $cod_mat;?>" readonly style="background: white;">
									</div>

									<div class="selector-cod_mat" id="sp5">   
										<select style="width:50%;;height: 38px;margin-top:23px"></select>      
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
										<label for="lapso">Lapso</label>  
										<input id="lapso" name="lapso" type="text" class="form-control" required value="<?php echo $lapso;?>" readonly style="background: white;">
									</div>

									<div class="selector-lapso">   
										<select style="width:50%;;height: 38px;margin-top:25px"></select>      
									</div>
								</div>


								<div class="form-group">
									<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
										<label for="seccion">Seccion</label>  
										<input id="seccion" name="seccion" type="text" class="form-control" required value="<?php echo $seccion;?>" readonly style="background: white;">
									</div>

									<div class="selector-seccion" id="sp6">   
										<select style="width:270px;height: 38px;margin-top:25px"></select>      
									</div>
								</div>						


							</p>						

							<p>
								<div class="form-group">
									<div class="col-md-12">
										<input type="submit" class="btn btn-primary" name="submit" value="Actualizar" style="background: #0C4783;width:120px"/> 
										<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>

									</div>								
								</div>
							</p>


						</form>
					</div>
				</fieldset>
			</form>

			<script>
				$(document).ready(function(){   



					$.ajax({            
						type: "POST",
						url: "getdocente.php",        
						success: function(response)
						{
							$('.selector-docente select').html(response).fadeIn();
						}
					});  

					buscarmateria($('#pensum').val());      


					function buscarmateria(val1){
						var parametros = {
							"pensum":val1
						}
						$.ajax({
							data:parametros,
							type: "POST",
							url: "getmateria.php",        
							success: function(response)
							{
								$('.selector-cod_mat select').html(response).fadeIn();
							}
						});
					}


					$('.selector-cod_mat select').change(function(){
						var v = $(this).val(); 
						$('#cod_mat').val(v);       
					}); 


					$.ajax({
						type: "POST",
						url: "getnumseccion.php",
						success: function(response)
						{
							$('.selector-seccion select').html(response).fadeIn();
						}
					});

					$('.selector-seccion select').change(function(){
						var v = $(this).val(); 
						$('#seccion').val(v);       
					});


					$('.selector-docente select').change(function(){
						var v = $(this).val(); 
						$('#cod_doc').val(v);       
					}); 

					$.ajax({
						type: "POST",
						url: "getlapso2.php",
						success: function(response)
						{
							$('.selector-lapso select').html(response).fadeIn();
						}
					});

					$('.selector-lapso select').change(function(){
						var v = $(this).val(); 
						$('#lapso').val(v);       
					}); 


				});
			</script>

		</body>
		</html>
