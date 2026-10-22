
<?php

session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['copiar_seccion']==1) {
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
?>
<html>
<head>
	<style>

		#marco
		{    
			
			max-width: 800px;
			min-width: 800px;
		}

		#editable_table {
			border-collapse: separate;
			background: #fff;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			border-radius: 5px;

		}


	</style>
</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="buscar5.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-copy"></span> Copiar Seccion</label>
				</div>
				


				<table style="margin-left: 0px;">
					

					<tr>							

						<td style="width:50%;">
							<p>         

								<div class="panel panel-default" style="margin-left: 10px;background:#DBDADF;width:95%">
									<div class="panel-body">
										<center><h5>Seccion de origen</h5></center> 
										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;">
												<label for="cod_doc" style="margin-left: 15px;">Docente</label> 
												<input id="cod_doc" name="cod_doc" type="hidden">
											</div>

											<div class="selector-docente" id="sp1">   
												<select style="width:83%;height: 38px;margin-top:28px;margin-left: 0px;"></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="cod_mat" style="margin-left: 15px;">Materia</label>  
												<input id="cod_mat" name="cod_mat" type="hidden">
											</div>

											<div class="selector-cod_mat" id="sp5">   
												<select style="width:83%;height: 38px;margin-top:28px;margin-left: 0px;" required></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="lapso" style="margin-left: 15px;">Lapso</label>  
												<input id="lapso" name="lapso" type="hidden">
											</div>

											<div class="selector-lapso" id="sp2">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="seccion" style="margin-left: 15px;">Seccion</label>  
												<input id="seccion" name="seccion" type="hidden">
											</div>

											<div class="selector-seccion" id="sp6">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>
									</div>
								</div>
							</p>
						</td>

						<td style="width:50%">
							<p>     
								<div class="panel panel-default" style="margin-left: 10px;background:#DBDADF;width:95%">
									<div class="panel-body">
										<center><h5>Seccion de destino</h5></center>     
										<input id="cod_doc2" name="cod_doc2" type="hidden">
										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;">
												<label for="cod_doc2" style="margin-left: 15px;">Docente</label>  
												
											</div>

											<div class="selector-docente2" id="sp1b">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="cod_mat2" style="margin-left: 15px;">Materia</label>  
												<input id="cod_mat2" name="cod_mat2" type="hidden">
											</div>

											<div class="selector-cod_mat2" id="sp5b">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="lapso2" style="margin-left: 15px;">Lapso</label>  
												<input id="lapso2" name="lapso2" type="hidden">
											</div>

											<div class="selector-lapso2" id="sp2b">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12" style="width: 0%;margin-left: 0px;margin-top:0px">
												<label for="seccion2" style="margin-left: 15px;">Seccion</label>  
												<input id="seccion2" name="seccion2" type="hidden">
											</div>

											<div class="selector-seccion2" id="sp6b">   
												<select style="width:83%;height: 38px;margin-top:28px" required></select>      
											</div>
										</div>
									</div>
								</div>

								<tr>
									<td>
										<div class="form-group">
											<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
												<a type="button" id="btnbuscar" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Buscar</a>
												<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
												<input type="submit" id="btnbuscar3" class="btn btn-primary" name="submit" value="Copiar seccion" style="background: #0C4783;"/> 
											</div>
										


											<div class="col-lg-10" style="position: absolute;margin-left: 400px;margin-top:0px;width: 250px">
												<div class="radio">
													<label>
														<input type="radio" name="Radios" id="optionsRadios1" value="A" checked="" style="width:20px">
														Sin notas
													</label>							

													<label>
														<input type="radio" name="Radios" id="optionsRadios2" value="B" style="width:20px;margin-left: -10px;">
														Con notas
													</label>
												</div>
											</div>
										</div>
									</td>
								</tr> 
							</p>
						</td>
					</tr>						
				</table>
				<table id="editable_table" class="table table-bordered table-striped">

				</table></center>
			</fieldset>
		</form>
	</div>



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

            $('#cod_doc').val('');
            $('#cod_mat').val(''); 
            $('#lapso').val(''); 
            $('#seccion').val('');
           

            $('#cod_doc2').val('');
            $('#cod_mat2').val(''); 
            $('#lapso2').val(''); 
            $('#seccion2').val('');
           


			hide_loading_message();

			$.ajax({
				type: "POST",
				url: "getdocente_x1.php",
				success: function(response)
				{
					$('.selector-docente select').html(response).fadeIn();
				}
			});


			$("#btnbuscar").click(function() {       
				enviar_datos($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val(),$(".selector-seccion select").val());
			});


			function enviar_datos(val1,val2,val3,val4){
				show_loading_message();
				var parametros = {
					"cod_doc":val1,
					"cod_mat":val2,
					"lapso":val3,
					"seccion":val4

				}       

				$.ajax({
					data:parametros,
					type: "post",
					url: "buscar5b.php",         
					success: function(datos)
					{        
						$('#editable_table').html(datos);
						hide_loading_message();
					}
				});
			} 

			$.ajax({
				type: "POST",
				url: "getdocente_x1.php",
				success: function(response)
				{
					$('.selector-docente2 select').html(response).fadeIn();
				}
			});

			$('#sp1 select').click(function(){
				var v = $(this).val(); 
				$('#cod_doc').val(v);       
			}); 

			$('#sp1b select').click(function(){
				var v = $(this).val(); 
				$('#cod_doc2').val(v);      
			}); 

			$('#sp5 select').click(function(){
				var v = $(this).val();     
				$('#cod_mat').val(v); 

			}); 

			$('#sp5b select').click(function(){
				var v = $(this).val();     
				$('#cod_mat2').val(v); 

			}); 



			$('#sp2 select').click(function(){
				var v = $(this).val();     
				$('#lapso').val(v); 
				buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());

			}); 

			$('#sp2b select').click(function(){
				var v = $(this).val();     
				$('#lapso2').val(v); 
				$('#seccion2').val('');     
				$(".selector-seccion2 select").empty();
				buscarseccion2($(".selector-docente2 select").val(),$(".selector-cod_mat2 select").val(),$(".selector-lapso2 select").val());

			}); 


			$(".selector-docente select").change(function() {

				$('#cod_mat').val('');
				$(".selector-cod_mat select").empty();
				$('#lapso').val('');
				$(".selector-lapso select").empty();

				buscarmateria($(".selector-docente select").val());


			}); 


			$(".selector-docente2 select").change(function() {

				$('#cod_mat2').val('');
				$(".selector-cod_mat2 select").empty();
				$('#lapso2').val('');
				$(".selector-lapso2 select").empty();

				buscarmateria2($(".selector-docente2 select").val());


			});

			function buscarmateria(val1){
				var parametros = {
					"cod_doc":val1

				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getseccion_x1.php",        
					success: function(response)
					{
						$('.selector-cod_mat select').html(response).fadeIn();
					}
				});
			}

			function buscarmateria2(val1){
				var parametros = {
					"cod_doc":val1

				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getseccion_x1.php",        
					success: function(response)
					{
						$('.selector-cod_mat2 select').html(response).fadeIn();
					}
				});
			}


			$(".selector-cod_mat select").change(function() {        

				$('#lapso').val('');
				$(".selector-lapso select").empty();

				buscarlapso($(".selector-docente select").val(),$(".selector-cod_mat select").val())


			});  

			$(".selector-cod_mat2 select").change(function() {       

				$('#lapso2').val('');
				$(".selector-lapso2 select").empty();
				$('#tiplap').val('');      
				$(".selector-tipolapso select").empty();
				buscarlapso2($(".selector-docente2 select").val(),$(".selector-cod_mat2 select").val())


			});  

			function buscarlapso(val1,val2){
				var parametros = {
					"cod_doc":val1,
					"cod_mat":val2,       
				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getlapso_x1.php",        
					success: function(response)
					{             
						$('.selector-lapso select').html(response).fadeIn();
					}
				});
			}



			function buscarlapso2(val1,val2){
				var parametros = {
					"cod_doc":val1,
					"cod_mat":val2,       
				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getlapso_x1.php",        
					success: function(response)
					{             
						$('.selector-lapso2 select').html(response).fadeIn();
					}
				});
			}


			$(".selector-lapso select").change(function() {        



				$('#seccion').val('');      
				$(".selector-seccion select").empty();
				buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
			});


			function buscartipolapso(val1,val2,val3){
				var parametros = {
					"cod_doc":val1,
					"cod_mat":val2,
					"lapso":val3
				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "gettipolapso.php",        
					success: function(response)
					{
						$('.selector-tipolapso select').html(response).fadeIn();
					}
				});
			}

			$('#sp3 select').click(function(){
				var v = $(this).val();     
				$('#tiplap').val(v);          

			});        



			$('#sp6 select').click(function(){
				var v = $(this).val();     
				$('#seccion').val(v);         

			});     


			$('#sp6b select').click(function(){
				var v = $(this).val();     
				$('#seccion2').val(v);          

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
					url: "getseccion2f_x1.php",        
					success: function(response)
					{
						$('.selector-seccion select').html(response).fadeIn();
					}
				});
			}

			function buscarseccion2(val1,val2,val3){
				var parametros = {
					"cod_doc":val1,
					"cod_mat":val2,
					"lapso":val3
				}
				$.ajax({
					data:parametros,
					type: "POST",
					url: "getseccion2f_x1.php",        
					success: function(response)
					{
						$('.selector-seccion2 select').html(response).fadeIn();
					}
				});
			}



  // Show loading message
  function show_loading_message(){
  	$('#marco').hide();
  	$('#loading_container').show();
  }
  // Hide loading message
  function hide_loading_message(){
  	$('#loading_container').hide();
  	$('#marco').show();
  }


});
</script>

</body>
</html>


