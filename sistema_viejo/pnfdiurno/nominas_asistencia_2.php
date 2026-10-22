
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
?>
<html>
<head>

	<style>
		#marco
		{      
			max-width: 450px;
			min-width: 220px;
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
		<form class="form-horizontal" id="effect2" method="post" action="CARGA DE NOTAS.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tags"></span> Listado de Alumnos</label>
				</div>

				<br>
				


				<p>            


					<div class="form-group">
						<div class="col-md-12" style="width:0%;margin-left: 20px;">
							<label for="pensum">Pensum</label>
							<input type="hidden" id="pensum" name="pensum">                     
						</div>

						<div class="selector-pensum" id="sp0">   
							<select style="width:85%;height: 38px;margin-top:26px;margin-left: -15px;"></select>      
						</div>
					</div>



					<div class="form-group">
						<div class="col-md-12" style="width:0%;margin-left: 20px;">
							<label for="cod_doc">Docente</label>
							<input type="hidden" id="cod_doc" name="cod_doc">                   
						</div>

						<div class="selector-cod_doc">   
							<select style="width:85%;height: 38px;margin-top:26px;margin-left: -15px;"></select> 
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12" style="width:0%;margin-left: 20px;">
							<label for="cod_mat">Materia</label>  
							<input type="hidden" id="cod_mat" name="cod_mat">                   
						</div>

						<div class="selector-cod_mat" id="sp5">   
							<select style="width:85%;height: 38px;margin-top:26px;margin-left: -15px;"></select>   
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12" style="width:0%;margin-left: 20px;">
							<label for="lapso">Lapso</label>  
							<input type="hidden" id="lapso" name="lapso">                   
						</div>

						<div class="selector-lapso" id="sp2">   
							<select style="width:85%;height: 38px;margin-top:26px;margin-left: -15px;"></select>  
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12" style="width:0%;margin-left: 20px;">
							<label for="seccion">Seccion</label>
							<input type="hidden" id="seccion" name="seccion">                   
						</div>


						<div class="selector-seccion">   
							<select style="width:85%;height: 38px;margin-top:26px;margin-left: -15px;"></select>    
						</div>
					</div> 


				<div class="form-group">
					<div class="col-md-12" style="margin-left:20px">
						<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="width:120px"/> 
						<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
					</div>
				</div>
		  
				</p>



		
	




</fieldset>
</form>
<!-- 	<div class="form-group" style="padding-left:15px;padding-right:15px;">

	<form class="form-horizontal" method="post" action="NOMINA DE ASISTENCIA ESCEL.php">
		<input type="hidden" id="cod_doc1" name="cod_doc1">
		<input type="hidden" id="cod_mat1" name="cod_mat1">
		<input type="hidden" id="lapso1" name="lapso1">
		<input type="hidden" id="seccion1" name="seccion1">
		<input type="hidden" id="tiplap1" name="tiplap1">


		<p>					
			<div class="form-group">
				<div class="col-md-12" style="width:50%;margin-left: 5px">
					<label for="url">Nombre de Archivo</label>  
					<input id="url" name="url" type="text" class="form-control" required value="Nomina asistencia">
				</div>							
			</div>	

		</p>

		<p> 
			
			<div class="form-group">
				<div class="col-md-12"> 
				<input type="submit" class="btn btn-primary" name="submit" value="Exportar a Excel" style="width:180px;margin-left: 5px"/> 
                </div>
		    </div>
		</p>		
	</form>
</div> -->
</div>





<script>
	$(document).ready(function(){     

		$('#pensum').val('');
		$('#cod_doc').val('');
		$('#cod_mat').val(''); 
		$('#lapso').val(''); 
		$('#seccion').val('');


		var valor1 = {
			"opcion":"pensum"
		}
		$.ajax({
			data:valor1,
			type: "POST",
			url: "getselect.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

		$('.selector-pensum select').change(function(){
			var v = $(this).val(); 
			$('#pensum').val(v);              
			$('#cod_doc').val('');
			$(".selector-cod_doc select").empty();
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			$('#seccion').val('');
			$(".selector-seccion select").empty(); 
			buscardocente($(".selector-pensum select").val());           
		}); 

		function buscardocente(val1){
			var parametros = {
				"pensum":val1,            
				"opcion":"getdocente",
				"campos":"pd"
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getselect.php",        
				success: function(response)
				{
					$(".selector-cod_doc select").html(response).fadeIn();
				}
			});  
		}

		$('.selector-lapso select').change(function(){
			var v = $(this).val();     
			$('#lapso').val(v); 
			buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
		}); 

		$(".selector-cod_doc select").change(function() {
			var v = $(this).val();     
			$('#cod_doc').val(v);
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			buscarmateria($(".selector-pensum select").val(),$(".selector-cod_doc select").val());
		}); 


		function buscarmateria(val1,val2){
			var parametros = {
				"pensum":val1,
				"cod_doc":val2,
				"opcion":"getmateria2"
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getselect.php",        
				success: function(response)
				{
					$('.selector-cod_mat select').html(response).fadeIn();
				}
			});
		}


		$(".selector-cod_mat select").change(function() {  
			var v = $(this).val();     
			$('#cod_mat').val(v);     
			buscarlapso($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val())

			$('#lapso').val('');
			$(".selector-lapso select").empty();
		});   

		function buscarlapso(val1,val2){
			var parametros = {
				"cod_doc":val1,
				"cod_mat":val2,
				"opcion":"getlapso", 
				"campos":"dm"      
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getselect.php",        
				success: function(response)
				{             
					$('.selector-lapso select').html(response).fadeIn();
				}
			});
		}

		$(".selector-lapso select").change(function() {       
			buscartipolapso($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
			$('#seccion').val('');      
			$(".selector-seccion select").empty();
			buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
		});

		$('.selector-seccion select').change(function(){
			var v = $(this).val();     
			$('#seccion').val(v);         

		}); 

		function buscarseccion(val1,val2,val3){
			var parametros = {
				"cod_doc":val1,
				"cod_mat":val2,
				"lapso":val3,
				"campos":"dml",
				"opcion":"getnumseccion3" 
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getselect.php",        
				success: function(response)
				{
					$('.selector-seccion select').html(response).fadeIn();
				}
			});
		}


	});
</script>

</body>
</html>
