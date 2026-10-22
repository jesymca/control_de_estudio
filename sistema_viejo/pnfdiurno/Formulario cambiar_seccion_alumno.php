
<?php
session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['cambiar_seccion']==1) {
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
if ($id=="") {
	$id=$_SESSION['id'];
}
include "db.php";
$query = "SELECT * FROM alumno WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$cedula=$row["cedula"];
	$carrera=$row["carrera"];
}
include('/Classes/class_api.php');
$x=new PDF();
$pensum = $x->pensum($carrera);

?>
<html>
<head>
	<style>

		#marco{
			width:70%;                
		}


	</style>
</head>
<body>

	<!--Formulario-->
	<div class="container" id="marco">
		<form class="form-horizontal" name="informacion" id="effect2" method="post" action="remplazar_seccion2.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-list"></span> Cambiar Seccion</label>
					<input type="hidden" id="id" name="id" value="<?php echo $id?>">
					<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">
					<input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>">
				</div>
				<!--inicio de la Tabla-->
				<table class="responstable">
					<thead>
						<tr>
							<th width="1%">Codigo</th>
							<th width="12%">Cedula</th>
							<th width="70%">Nombre</th>   

						</tr>
					</thead>
					<tbody>
						<?php
						$query = "SELECT * FROM alumno WHERE id='".$id."'";
						$result = mysqli_query($conn, $query);
						while($row = mysqli_fetch_array($result))
						{
							$_SESSION['id']=$row["id"];
							$_SESSION['pensum']=$row["carrera"].$row["mencion"].$row["plan"];
							$cedula=$row["cedula"];
							$carrera= $row["carrera"];

							echo '<tr>
							<td >'.$row["id"].'</td>
							<td>'.$row["cedula"].'</td>
							<td>'.$row["nombre"].'</td>                                     
						</tr>';
					}     
					?>
				</tbody>
			</table>
			<!--Fin de la Tabla-->

			<table>	
				<tr>			
					<td>

						<div class="form-group">
							<div class="col-md-12" style="margin-left: 10px;margin-top: -15px">
								<label for="lapso">Lapso</label> 								
								<input type="hidden" id="lapso" name="lapso">                   
							</div>

							<div class="selector-lapso" style="margin-left: 25px;height: 38px;">   
								<select style="margin-left: 0px;width: 150px;height: 38px;" required></select>  
							</div>
						</div>
					</td>
					<td style="width: 10px"></td>
					<td>


						<div class="form-group">
							<div class="col-md-12" style="margin-left: 10px;">
								<label for="seccion">Seccion actual</label>
								<input type="hidden" id="seccion" name="seccion">                   
							</div>


							<div class="selector-seccion">  
								<select style="margin-left: 25px;width: 150px;height: 38px;" required>
									<option value="">Seleccionar</option>
									<?php
									$x->listado_de_secciones($pensum);
									?>
								</select>      
							</div> 
						</div> 

					</td>
					<td style="width: 10px"></td>
					<td>
						<div class="form-group">
							<div class="col-md-12" style="margin-left: 10px;">
								<label for="seccion2">Seccion nuevo</label>
								<input type="hidden" id="seccion2" name="seccion2">                   
							</div>


							<div class="selector-seccion2" style="margin-left: 0px;">   
								<select style="margin-left: 25px;width: 150px;height: 38px;" required>
									<option value="">Seleccionar</option>
									<?php
									$x->listado_de_secciones($pensum);
									?>
								</select>  
							</div>
						</div> 
					</td>
				</tr>

			</table>

			<br />


			<div class="form-group" style="margin-left: 5px;margin-top: -15px">
				<div class="col-md-12">
					<input type="submit" class="btn btn-primary" name="submit" value="Cambiar" style="width: 120px;right: 10px"/> 						
					<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>



		</form>
	</div>
</fieldset>
</form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="Formulario Lismat_cod_mat_combo_buscar.js"></script>

<script type="text/javascript">
	$(document).ready(function() {


		$('#lapso').val(''); 		
		$('#seccion').val('');		
		
		
		$('.selector-lapso select').click(function(){
			var v = $(this).val();     
			$('#lapso').val(v); 			
		}); 

		buscarlapso($("#cedula").val());


		function buscarlapso(val1){
			var parametros = {
				"cedula":val1				      
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getlapso5.php",        
				success: function(response)
				{             
					$('.selector-lapso select').html(response).fadeIn();					
				}
			});
		}

		

		$(".selector-seccion2 select").change(function() { 
			var v = $(this).val();
			$('#seccion2').val(v);			
		});


		$('.selector-seccion select').click(function(){
			var v = $(this).val();     
			$('#seccion').val(v);         

		}); 

		


	});
</script>

</body>
</html>

