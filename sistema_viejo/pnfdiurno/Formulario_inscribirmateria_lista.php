<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['inscribir_materia']==1) {
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
?>

<?php include 'menu.php';?>

<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" /> 
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/layout.css">

<script charset="utf-8" src="js/dataTables.bootstrap.min.js"></script>
<script charset="utf-8" src="js/jquery.dataTables.min.js"></script>
<script charset="utf-8" src="js/jquery.validate.min.js"></script>
<script charset="utf-8" src="Formulario_inscribirmateria_data.js"></script>


<style type="text/css">
	body
	{
		/*background-image: url("fondo.jpg");*/
		background-attachment: fixed;
		background-size:cover;
		background-repeat:no-repeat;
		background-position: center center;
		background:#272822;
	}

	#marco
	{
		width:1020px;
		min-width: 1020px;
	}

</style>
</head>
<body>

	<?php include 'nav1.php';?>



</head>
<body>




	<div class="container" id="marco">

		<fieldset>
			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario">Inscribir materia</label>
			</div>
			<br />

			<table>
				<tr>
					<td style="width: 30px;"></td>
					<td style="width: 120px;">

						<a id="add_company" name="add_company" class="btn btn-primary" style="background: #0C4783;width:120px">Agregar</a>
					</td>
					<td style="width: 10px;"></td>
					<td style="width: 120px;">

						<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
					</td>


				</tr>
			</table>



			<table class="datatable" id="table_companies">
				<thead>
					<tr>
						<th width="1%">Alumno</th>
						<th width="1%">Codigo</th>
						<th width="10%">Materia</th>
						<th width="1%">Seccion</th>
						<th width="1%">Lapso</th>
						<th width="1%">Tipo</th>
						<th width="1%">Docente</th>
						<th width="1%">Funciones</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>

		</div>
	</fieldset>
</form>
</div>

<div class="lightbox_bg"></div>

<div class="lightbox_container" style="width: 70%;height:80%;">
	<div class="lightbox_close"></div>
	<div class="lightbox_content">


		<h2>Agregar Inscribirmateria</h2>
		<form method="post" action="Formulario notas_insert2.php">

			<div class="input_container">
				<label for="pensum">Pensum:</label>
				<div class="field_container">
					<input type="text" class="text" name="pensum" id="pensum" tabindex="1" required style="width:30%" pattern="^[IXC|GXC|MXC]{3,3}$" title="Ejemplo:IXC ó GXC ó MXC">
				</div>
			</div>

			<div class="selector-pensum" id="sp0">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div> 


			<div class="input_container">
				<label for="codigo">Codigo:</label>
				<div class="field_container">
					<input type="text" class="text" name="codigo" id="codigo" tabindex="1" required style="width:30%" pattern="^[V]\d{8}$" title="Ejemplo:V12345678">
				</div>
			</div>

			<div class="selector-codigo" id="sp1">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div>  

			<div class="input_container">
				<label for="cod_doc">Docente:</label>
				<div class="field_container">
					<input type="text" class="text" name="cod_doc" id="cod_doc" tabindex="6" required style="width:30%" pattern="[0-9]{1,5}"> 
				</div>
			</div>

			<div class="selector-cod_doc" id="sp2">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div>


			<div class="input_container">
				<label for="cod_mat">Materia:</label>
				<div class="field_container">
					<input type="text" class="text" name="cod_mat" id="cod_mat" tabindex="2" required style="width:30%">
				</div>
			</div>

			<div class="selector-cod_mat" id="sp7">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div>


			<div class="input_container">
				<label for="lapso">Lapso:</label>
				<div class="field_container">
					<input type="text" class="text" name="lapso" id="lapso" tabindex="4" required style="width:30%">
				</div>
			</div>

			<div class="selector-lapso" id="sp5">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div> 






			<div class="input_container">
				<label for="seccion">Seccion:</label>
				<div class="field_container">
					<input type="text" class="text" name="seccion" id="seccion" tabindex="3" required style="width:30%">
				</div>
			</div>



			<div class="selector-seccion" id="sp8">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div>


			<div class="input_container">
				<label for="tiplap">Tipo de lapso:</label>
				<div class="field_container">
					<input type="text" class="text" name="tiplap" id="tiplap" tabindex="5"  style="width:30%">
				</div>
			</div>

			<div class="selector-tiplap" id="sp6">   
				<select style="width:31%;height: 38px;margin-top:-52px;margin-left: 330px;position: absolute;"></select>      
			</div>


			<div class="button_container">
				<input type="submit" class="btn btn-primary" name="submit" value="Agregar" style="background: #0C4783;width:120px"/> 

			</div>
		</form>

	</div>
</div>

<noscript id="noscript_container">
	<div id="noscript" class="error">
		<p>JavaScript support is needed to use this page.</p>
	</div>
</noscript>

<div id="message_container">
	<div id="message" class="success">
		<p>This is a success message.</p>
	</div>
</div>

<div id="loading_container">
	<div id="loading_container2">
		<div id="loading_container3">
			<div id="loading_container4">
				Cargando por favor espere...
			</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function(){


		$.ajax({
			type: "POST",
			url: "getpensum.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

		$('#sp0 select').click(function(){
			var v = $(this).val(); 
			$('#pensum').val(v); 
			$('#codigo').val('');     
			$(".selector-codigo select").empty();       
			buscaralumnos($(".selector-pensum select").val());      


		}); 

		function buscaralumnos(val1){

			var parametros = {
				"pensum":val1

			}

			$.ajax({
				data:parametros,
				type: "POST",
				url: "getalumnopensum.php",
				success: function(response)
				{
					$('.selector-codigo select').html(response).fadeIn();
				}
			});

		}



		$.ajax({     
			type: "POST",
			url: "getdocente.php",
			success: function(response)
			{
				$('.selector-cod_doc select').html(response).fadeIn();
			}
		});



		$('#sp1 select').click(function(){
			var v = $(this).val(); 
			$('#codigo').val(v);       
		}); 



		$('#sp2 select').click(function(){
			var v = $(this).val(); 
			$('#cod_doc').val(v); 

			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			$('#tiplap').val('');     
			$(".selector-tiplap select").empty();

			buscarmateria($(".selector-cod_doc select").val());

		});


		function buscarmateria(val1){

			var parametros = {
				"cod_doc":val1

			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getseccion3.php",        
				success: function(response)
				{
					$('.selector-cod_mat select').html(response).fadeIn();
				}
			});
		}


		$('#sp7 select').click(function(){       
			var v = $(this).val(); 
			$('#cod_mat').val(v);      
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			$('#tiplap').val('');     
			$(".selector-tiplap select").empty();


			buscarlapso($('.selector-cod_doc select').val(),$('.selector-cod_mat select').val());
			$('#seccion').val('');     
			$(".selector-seccion select").empty();



		});  

		function buscarlapso(val1,val2){

			var parametros = {
				"cod_doc":val1,            
				"cod_mat":val2

			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getlapso3.php",  
				success: function(response)
				{


					$('.selector-lapso select').html(response).fadeIn();
				}
			});
		}


		$('#sp5 select').click(function(){
			var v = $(this).val(); 
			$('#lapso').val(v); 
			buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());     


			$('#tiplap').val('');     
			$(".selector-tiplap select").empty();       
			buscartipolapso($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());     

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
				url: "gettipolapso3.php",  
				success: function(response)
				{

					$('.selector-tiplap select').html(response).fadeIn();
				}
			});
		}





		$('#sp6 select').click(function(){       
			var v = $(this).val(); 
			$('#tiplap').val(v);

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
				url: "gettipolapso3sec.php",  
				success: function(response)
				{

					$('.selector-seccion select').html(response).fadeIn();
				}
			});
		}

		$('#sp8 select').click(function(){       
			var v = $(this).val(); 
			$('#seccion').val(v);             
		}); 



});
</script>
</body>
</html>




