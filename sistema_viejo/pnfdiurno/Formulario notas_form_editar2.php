
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
include 'menu.php';

if ($_POST['codigo1']=="") {
	$cedula = $_SESSION['alumno_sec'];
}else{
	$_SESSION['alumno_sec']= $_POST['codigo1'];
	$cedula = $_SESSION['alumno_sec'];
}
require("db.php");
$query = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$actividad=$row["actividad"];
	$carrera=$row["carrera"];
}

switch ($carrera) {	
	case 'I':
	$pensum="IXC";
	break;
	case 'M':
	$pensum="MXC";
	break;
	case 'T':
	$pensum="TXC";
	break;
	case 'E':
	$pensum="EXC";
	break;
	case 'G':
	$pensum="GXC";
	break;
	default:		
	break;
}

$cod_doc="303";


if ($actividad === "1") {

	?>
	<html>
	<head>
		<style>
			#marco{
				width:800px;
				min-width: 800px;		
				max-width: 800px;		
			}
			#titulo_formulario{
				width:100%;
			}

		</style>
	</head>
	<body>

		<!--Formulario-->
		<div class="container" id="marco">
			<form class="form-horizontal" name="informacion" id="effect2" method="post" action="Formulario notas_insertc.php">
				<fieldset>
					<div class="form-group" id="titulo_formulario"> 
						<label id="titulo_formulario">Modificar nota</label>
						<INPUT type="hidden" id="codigo1" name="codigo1" value="<?php echo $cedula;?>">	
						</div>
						<br />

						<table> 
							<tr>
								<td style="width: 20px;"></td>
								<td style="width: 250px;">
									<div class="form-group">
										<div class="col-md-12">
											<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="background: #0C4783;width:120px"/> 
											<a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
										</div>
									</div>
								</td>

							</tr>
						</table> 

						<table> 
							<tr>
								<td style="width: 20px;"></td> 

								<td style="width: 70px;">
									<div class="form-group">
										<div class="col-md-12" style="width: 150px;margin-top:0px">
											<label for="codigo">Codigo</label>
											<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">											
											<input type="hidden" id="codigo" name="codigo" value="<?php echo $cedula?>">  
											<input id="codigo1" name="codigo1" type="text" placeholder="Codigo" value="<?php echo $cedula?>" class="form-control" style="background-color:#F0F0F0">
										</div>
									</div>
								</td> 
								<td style="width: 25px;"></td> 
								
								<td style="width: 180px;"> 

									<div class="form-group">
										<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
											<label for="cod_doc">Docente</label>
											<input type="hidden" id="cod_doc" name="cod_doc">					
										</div>

										<div class="selector-docente" id="sp1">   
											<select style="width:100%;height: 38px;" value=>
											<OPTION value="<?php echo $cod_doc?>">	
											</select> 
										</div>
									</div>
									

								</td>
								<td style="width: 40px;"></td> 

								<td style="width: 350px;">


									<div class="form-group">
										<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
											<label for="cod_mat">Materia</label>  
											<input type="hidden" id="cod_mat" name="cod_mat">					
										</div>

										<div class="selector-cod_mat" id="sp5">   
											<select style="width:100%;height: 38px;"></select>   
										</div>
									</div>


								</td>						
							</tr>
						</table> 

						<table> 
							<tr> 
								<td style="width: 35px;"></td>

								

								<td style="width: 70px;"> 

									<div class="form-group">
										<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
											<label for="lapso">Lapso</label>  
											<input type="hidden" id="lapso" name="lapso">					
										</div>

										<div class="selector-lapso" id="sp2">   
										<select style="width:120px;height: 38px;"></select>  
										</div>
									</div>				
									

								</td> 
                                <td style="width: 40px;"></td>
								<td style="width: 100px;">

									<div class="form-group">
										<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
											<label for="seccion">Seccion</label>
											<input type="hidden" id="seccion" name="seccion">					
										</div>


										<div class="selector-seccion" id="sp6">   
											<select style="width:91%;height: 38px;"></select>  
										</div>
									</div>  

								</td>

								<td style="width: 30px;"></td>
								<td style="width: 100px;">
	
									<div class="form-group">
										<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
											<label for="nota">Nota</label>
											<input type="hidden" id="nota" name="nota">					
										</div>

										<div class="selectnota" id="sp7">   
											<select style="width:91%;height: 38px;margin-top:0px;"></select>  
										</div>
									</div>  

								</td>

								<td style="width: 10px;"></td> 

								<td style="width: 80px;">
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-top:0px">
											<label for="acu">Acu</label>
											<input type="hidden" id="acu" name="acu">  
											<input id="acu1" name="acu1" type="text" placeholder="Acu" class="form-control" style="background-color:#F0F0F0;">
										</div>
									</div>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</fieldset>
		
		<script src="js/jquery-3.1.1.min3.js"></script>
		<script src="js/bootstrap.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {

				buscardocente($('#pensum').val());

				function buscardocente(val1){
					var parametros = {
						"pensum":val1

					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getdocente_x.php",
						success: function(response)
						{
							$('.selector-docente select').html(response).fadeIn();
						}
					});
				}


               	$(".selector-docente select").change(function() {
                    var v = $(this).val();     
					$('#cod_doc').val(v); 

					$('#cod_mat').val('');
					$(".selector-cod_mat select").empty();
					$('#lapso').val('');
					$(".selector-lapso select").empty();
					buscarmateria($('#pensum').val(),$(".selector-docente select").val());
				}); 

		
                $('#sp5 select').click(function(){
					var v = $(this).val();     
					$('#cod_mat').val(v);					
				}); 


				$('#sp2 select').click(function(){
					var v = $(this).val();     
					$('#lapso').val(v); 
					buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());

				}); 








				function buscarmateria(val1,val2){
					var parametros = {
						"pensum":val1,
						"cod_doc":val2

					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getseccion_x.php",        
						success: function(response)
						{
							$('.selector-cod_mat select').html(response).fadeIn();
						}
					});
				}



				$(".selector-cod_mat select").change(function() {       
					buscarlapso($('#pensum').val(),$(".selector-docente select").val(),$(".selector-cod_mat select").val())

					$('#lapso').val('');
					$(".selector-lapso select").empty();
				});   

				function buscarlapso(val1,val2,val3){
					var parametros = {
						"pensum":val1,
						"cod_doc":val2,
						"cod_mat":val3,       
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getlapso.php",        
						success: function(response)
						{             
							$('.selector-lapso select').html(response).fadeIn();
						}
					});
				}


				$(".selector-lapso select").change(function() {       
					buscartipolapso($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());


					$('#seccion').val('');      
					$(".selector-seccion select").empty();
					buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
				});


				$('#sp6 select').click(function(){
					var v = $(this).val();     
					$('#seccion').val(v);         

				}); 

				function buscarseccion(val1,val2,val3){
					var parametros = {
						"cod_doc":val1,
						"cod_mat":val2,
						"lapso":val3
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getseccion2f.php",        
						success: function(response)
						{
							$('.selector-seccion select').html(response).fadeIn();
						}
					});
				}

			$(".selectnota select").change(function() {       
					var v = $(this).val(); 
			$('#nota').val(v);
			var nota = $("#nota").val();
			if(nota>20){
				swal("Nota invalida");                
			}
			else{
				calcular(nota);
			}
				});

	

		$('#selectlapso').click(function(){
			var v = $(this).val(); 
			$('#lapso').val(v);
		});

	


		


		

		$("#nota").change(function() {
			var nota = $("#nota").val();  
			calcular(nota);
		});

		function calcular(valor) {     
			total= parseInt(valor)*5; 
			if(total>=0){
				document.getElementById('acu').value=total;
				document.getElementById('acu1').value=total;
			}else{
				document.getElementById('acu').value=0; 
				document.getElementById('acu1').value=0; 
			}

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


	<?php
} else {
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


	</style>
</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS2.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> El alumno no esta activo</label>
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