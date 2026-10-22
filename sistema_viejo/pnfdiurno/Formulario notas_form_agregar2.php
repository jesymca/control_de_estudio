
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
include 'menu.php';

$cedula = $_POST['codigo1'];
?>
<html>
<head>
	<style>
		#marco{
			width:70%;
			min-width: 880px;			
			max-width: 880px;			
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
					<label id="titulo_formulario">Agregar nota</label>
					<INPUT type="hidden" id="codigo1" name="codigo1" value="<?php echo $cedula;?>">	
					</div>
					<br />

					<table> 
						<tr>
							<td style="width: 595px;"></td>
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
									<div class="col-md-12" style="width: 140px;margin-top:0px">
										<label for="codigo">Codigo</label>  
										<input id="codigo" name="codigo" type="text" placeholder="Codigo" value="<?php echo $cedula?>" class="form-control" style="background-color:#F0F0F0">
									</div>
								</div>
							</td> 
							<td style="width: 10px;"></td> 

							<td style="width: 200px;"> 
								<div class="selector-tabla_03">
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-left: 0%;margin-top:15px">
											<label for="cod_doc">Docente</label>  
											<input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" required style="background-color:#F0F0F0;">
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
							<td style="width: 10px;"></td> 
							<td style="width: 180px;">
								<div class="selector-tabla_01">
									
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-top:14px">
											<label for="cod_mat">Materia</label>  
											<input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" class="form-control" required style="background-color:#F0F0F0" pattern = "[A-Z0-9]{5,5}">
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-left: 100px;margin-top: -52px;">
											<select class="form-control" id="selectcod_mat" required style="background-color:#F0F0F0">											
											</select>
										</div>
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
										<div class="col-md-12" style="width: 120px;margin-top:15px">
											<label for="lapso">Lapso</label>  
											<input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control" required style="background-color:#F0F0F0;">
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

							<td style="width: 120px;">
								<div class="selector-selectseccion">
									<div class="form-group">
										<div class="col-md-6" style="width: 80px;margin-top: 14px;">
											<label>Seccion</label>
											<input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control input-md" required style="background-color:#F0F0F0">
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-12" style="width: 80px;margin-top: -52px;margin-left: 60px">
											<select class="form-control" id="sp1" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>

							<td style="width: 10px;"></td>
							

							<td style="width: 180px;"> 
								<div class="selector-tabla_04">
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-top:15px;margin-left: -3px">
											<label for="tiplap">Tipo de lapso</label>  
											<input id="tiplap" name="tiplap" type="text" placeholder="Tiplap" class="form-control" style="background-color:#F0F0F0;">
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-top: -52px;margin-left: 99px">
											<select class="form-control" id="selecttiplap" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>

							<td style="width: 10px;"></td>


							<td style="width: 180px;">
								<div class="selector-tabla_05">
									<div class="form-group">
										<div class="col-md-6" style="width: 120px;margin-top: 14px;">
											<label>Nota</label>
											<input id="nota" name="nota" type="text" placeholder="Nota" class="form-control input-md" required style="background-color:#F0F0F0;">
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12" style="width: 120px;margin-top: -52px;margin-left: 100px">
											<select class="form-control" id="selectnota" style="background-color:#F0F0F0">											
											</select>
										</div>
									</div>
								</div>
							</td>

							<td style="width: 10px;"></td> 

							<td style="width: 80px;">
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top:0px">
										<label for="acu">Acu</label>  
										<input id="acu" name="acu" type="text" placeholder="Acu" class="form-control" style="background-color:#F0F0F0;">
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
<!-- <script src="Formulario Lismat_cod_mat_combo_buscar.js"></script>
-->
<script type="text/javascript">
	$(document).ready(function() {



		$.ajax({
			type: "POST",
			url: "Formulario Docente_cod_doc_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_03 select').html(response).fadeIn();
			}
		});



	
			$.ajax({			
				type: "POST",
				url: "getseccion_0.php",
				success: function(response)
				{
					$('.selector-tabla_01 select').html(response).fadeIn();
				}
			});
	

	
	
			$.ajax({			
				type: "POST",
				url: "getlapso_0.php",				
				success: function(response)
				{							
					$('.selector-tabla_02 select').html(response).fadeIn();
				}
			});
	

	



	

		
			$.ajax({				
				type: "POST",
				url: "gettipolapso_0.php",				
				success: function(response)
				{
					$('.selector-tabla_04 select').html(response).fadeIn();
				}
			});
	

	
			$.ajax({				
				type: "POST",
				url: "getseccion2f_0.php",				
				success: function(response)
				{
					$('.selector-selectseccion select').html(response).fadeIn();
				}
			});
	

	

		$('#sp1').click(function(){
			var v = $(this).val(); 
			$('#seccion').val(v);
		}); 

		$('#selectcod_doc').click(function(){
			var v = $(this).val(); 
			$('#cod_doc').val(v);
		}); 

		$('#selectcod_mat').click(function(){
			var v = $(this).val(); 
			$('#cod_mat').val(v);
		}); 

		$('#selectnota').click(function(){
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

		$('#selecttiplap').click(function(){
			var v = $(this).val(); 
			$('#tiplap').val(v);
		});


		$("#nota").change(function() {
			var nota = $("#nota").val();  
			calcular(nota);
		});

		function calcular(valor) {     
			total= parseInt(valor)*5; 
			if(total>=0){
				document.getElementById('acu').value=total;
			}else{
				document.getElementById('acu').value=0; 
			}

		}

		$.ajax({
			type: "POST",
			url: "Formulario Nota_nota_select_combo.php",
			success: function(response)
			{
				$('.selector-tabla_05 select').html(response).fadeIn();
			}
		});	



	});
</script>

</body>
</html>