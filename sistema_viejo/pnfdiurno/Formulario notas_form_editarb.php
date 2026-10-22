
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
include 'menu.php';
include 'db.php';

$id = intval($_GET['id']);

$query = "SELECT * FROM notas WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
	$id=$row["id"];
	$codigo=$row["codigo"];
	$cod_mat=$row["cod_mat"];
	$seccion=$row["seccion"];
	$nota=$row["nota"];
	$acu=$row["acu"];
	$lapso=$row["lapso"];
	$tiplap=$row["tiplap"];
	$cod_doc=$row["cod_doc"];
	$cod_usu = $row['cod_usu'];
	$carrera = substr($cod_mat, 0, 1);
}
$conn->close();

include('getpensum3_clase.php');
$con = new carreras();
$pensum= $con->pensum($carrera);


?>

<html>
<head>
	<style>

		#marco{
			width:63%;
			min-width: 930px;			
			max-width: 930px;
		}

	</style>
</head>
<body>

	<!--Formulario-->
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="Formulario notas_updateb.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario">Actualizar nota</label>
				</div>
				<br />

				<table> 
					<tr>
						<td style="width: 630px;"></td>
						<td style="width: 250px;">
							<div class="form-group">
								<div class="col-md-12">	
									<input type="submit" class="btn btn-primary" id="Actualizar" name="Actualizar" value="Actualizar" style="background: #0C4783;width:120px"/> 					
									<a href="Formulario_notas_tabla_index2.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table> 	
				
				<table> 
					<tr>
						<td style="width: 70px;display: none;">
							<div class="form-group">
								<div class="col-md-12" style="width: 115px;margin-top:0px;">
									<label for="id">Id</label>
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">   
									<input id="id" name="id" type="text" value="<?php echo $id?>" class="form-control" style="background-color:#F0F0F0;">
								</div>
							</div>
						</td> 

						<td style="width: 20px;"></td> 
						<td style="width: 70px;">
							<div class="form-group">
								<div class="col-md-12" style="width: 140px;margin-top:0px">
									<label for="codigo">Codigo</label>  
									<input id="codigo" name="codigo" type="text" placeholder="Codigo" value="<?php echo $codigo?>" class="form-control" style="background-color:#F0F0F0"  readonly>
								</div>
							</div>
						</td> 
						<td style="width: 10px;"></td> 

						<td style="width: 180px;">
							<div class="selector-tabla_01">
								
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top:14px">
										<label for="cod_mat">Materia</label>  
										<input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" value="<?php echo $cod_mat?>" class="form-control" required style="background-color:#F0F0F0"  readonly>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-left: 100px;margin-top: -52px;">
										<select class="form-control" id="selectcod_mat" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>

							</div>
						</td>
						<td style="width: 10px;"></td> 

						<td style="width: 150px;">
							<div class="selector-selectseccion">

								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top:14px">
										<label for="seccion">Seccion</label>  
										<input id="seccion" name="seccion" type="text" placeholder="seccion" value="<?php echo $seccion?>" class="form-control" required style="background-color:#F0F0F0"  readonly>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 80px;margin-top: -52px;margin-left: 100px">
										<select class="form-control" id="sp1" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td>

						<td style="width: 10px;"></td>

						<td style="width: 140px;">
							
								<div class="form-group">
									<div class="col-md-6" style="width: 80px;margin-top: 0px;">
										<label>Nota</label>
										<input id="nota" name="nota" type="text" placeholder="Nota" value="<?php echo $nota?>" class="form-control input-md" required style="background-color:#F0F0F0;"  readonly>
									</div>
								

								<div class="selectnota">   
								<div class="col-md-12" style="width: 140px;margin-top: -38px;margin-left: 60px">
									<select style="width:91%;height: 38px;"></select>														
								</div>
								</div>
								</div> 

								
							
						</td>

						<td style="width: 10px;"></td> 

						<td style="width: 80px;">
							<div class="form-group">
								<div class="col-md-12" style="width: 120px;margin-top:-2px">
									<label for="acu">Acu</label>  
									<input id="acu" name="acu" type="text" placeholder="Acu" value="<?php echo $acu?>" class="form-control" style="background-color:#F0F0F0;"  readonly>
								</div>
							</div>
						</td>				
						
					</tr>
				</table> 

				<table> 
					<tr> 
						<td style="width: 20px;"></td> 
						<td style="width: 204px;"> 
							<div class="selector-tabla_02">
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top:0px">
										<label for="lapso">Lapso</label>  
										<input id="lapso" name="lapso" type="text" placeholder="Lapso" value="<?php echo $lapso?>" class="form-control" required style="background-color:#F0F0F0;" readonly>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top: -52px;margin-left: 100px">
										<select class="form-control" id="selectlapso" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td>

												

						<td style="width: 10px;"></td> 

						<td style="width: 200px;"> 
							<div class="selector-tabla_03">
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-left: 0%;margin-top:0px">
										<label for="cod_doc">Docente</label>  
										<input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" value="<?php echo $cod_doc?>" class="form-control" required style="background-color:#F0F0F0;"  readonly>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12" style="width: 400px;margin-top: -52px;margin-left: 100px">
										<select class="form-control" id="selectcod_doc" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</fieldset>
</form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="Formulario Lismat_cod_mat_combo_buscar.js"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$.ajax({
			type: "POST",
			url: "getnumseccion.php",
			success: function(response)
			{
				$('.selector-selectseccion select').html(response).fadeIn();
			}
		});

		$('#sp1').change(function(){
			var v = $(this).val(); 
			$('#seccion').val(v);

		}); 

		$('#selectcod_doc').change(function(){
			var v = $(this).val(); 
			$('#cod_doc').val(v);

		}); 


		$("#btnBuscar").click(function(){
			var valor = $('#buscar').val();
			obten_datos(valor);
		});






		$("#selectcod_mat").change(function() {
			var valor_01 = $("#selectcod_mat").val();
			llenar_combo_01(valor_01);
		});

		$.ajax({
			type: "POST",
			url: "Formulario Lismat_cod_mat_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_01 select').html(response).fadeIn();
			}
		});

		$("#selectlapso").change(function() {
			var valor_02 = $("#selectlapso").val();
			llenar_combo_02(valor_02);
		});

		$.ajax({
			type: "POST",
			url: "Formulario Lapso_lapso_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_02 select').html(response).fadeIn();
			}
		});


		$("#selectcod_doc").change(function() {
			var valor_03 = $("#selectcod_doc").val();
			llenar_combo_03(valor_03);
		});

        buscardocente($("#pensum").val());

		function buscardocente(val1){
			var parametros = {
				"pensum":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "Formulario Docente_cod_doc_select_combo.php",        
				success: function(response)
				{
					$(".selector-tabla_03 select").html(response).fadeIn();
				}
			});  
		}

	

		$("#selectcod_doc2").change(function() {
			var valor_03 = $("#selectcod_doc2").val();
			llenar_combo_03(valor_03);
		});

		$.ajax({
			type: "POST",
			url: "Formulario Docente_cod_doc_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_03b select').html(response).fadeIn();
			}
		});


		$("#selecttiplap").change(function() {
			var valor_04 = $("#selecttiplap").val();
			llenar_combo_04(valor_04);
		});

		$.ajax({
			type: "POST",
			url: "Formulario Tipos_lapso_tiplap_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_04 select').html(response).fadeIn();
			}
		});

	
		$('.selectnota select').change(function(){
        var v = $(this).val(); 
        $('#nota').val(v);       
        });

		$("#nota").change(function() {

			var nota = $("#nota").val();

			if(nota==="IN"){
				document.getElementById('acu').value=0;                       
			}
			else{
				calcular(nota);
			}

		});

		function calcular(valor) {      
			total= parseInt(valor)*5;
			document.getElementById('acu').value=total; 
		}

		$.ajax({
			type: "POST",
			url: "Formulario Nota_nota_select_combo.php",
			success: function(response)
			{
				$('.selectnota select').html(response).fadeIn();
			}
		});



	});
</script>

</body>
</html>
