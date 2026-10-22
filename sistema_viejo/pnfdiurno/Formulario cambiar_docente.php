
<?php
session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['cambiar_docente']==1) {
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
$codigo = $_POST['codigo'];
?>
<html>
<head>
	<style>

		#marco{
			width:50%;					
		}
		#titulo_formulario{
			width:100%;
		}

		     a{
			   margin-top:7px;			   
		      }



		@media (max-width: 992px) {
			  
              select{
              	width:80%;
              	height: 38px;
              	margin-top:0px;
              	margin-left: 10%;
              	margin-right: 10%;
              }
              label{
              	margin-left: 20px;
              }

     
		    

		  
             
        }

        @media (min-width: 993px) {            

              select{
              	width:90%;
              	height: 38px;
              	margin-top:0px;
              	margin-left: 4.5%;
              	margin-right: 5%;
              }
              label{
              	margin-left: 15px;
              }

         
              
           
             
         

        } 	


	</style>
</head>
<body>

	<!--Formulario-->
	<div class="container" id="marco">
		<form class="form-horizontal" name="informacion" id="effect2" method="post" action="">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-link"></span> Cambiar Docente</label>
				</div>


				<p> 
					<div class="form-group">
						<div class="col-md-12">
							<label for="pensum">Pensum</label>
							<input type="hidden" id="pensum" name="pensum">                     
						</div>

						<div class="selector-pensum">   
							<select></select>      
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12">
							<label for="cod_doc">Docente actual</label>  
							<input type="hidden" id="cod_doc" name="cod_doc">                   
						</div>

						<div class="selector-cod_doc">   
							<select></select>					     
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12">
							<label for="cod_doc2">Docente nuevo</label>  
							<input type="hidden" id="cod_doc2" name="cod_doc2">                   
						</div>

						<div class="selector-cod_doc2">   
							<select></select>  
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12">
							<label for="cod_mat">Materia</label>  
							<input type="hidden" id="cod_mat" name="cod_mat">                   
						</div>

						<div class="selector-cod_mat">   
							<select></select>   
						</div>
					</div>


					<div class="form-group">
						
						<div class="col-md-12">
							<label for="lapso">Lapso</label>
							<input type="hidden" id="lapso" name="lapso">                   
						</div>

						<div class="selector-lapso">   
							<select></select> 
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12">
							<label for="seccion">Seccion</label>
							<input type="hidden" id="seccion" name="seccion">                   
						</div>


						<div class="selector-seccion">   
							<select></select>    
						</div>
					</div> 
					
				</p>

				<div class="form-group" style="margin-left: 1px;">
					<div class="col-md-12">
						<a type="button" id="Buscar" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Buscar</a>
						<a type="button" id="actualizar" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
						<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
					</div>
				</div>

				<table id="editable_table" class="table table-bordered table-striped"></table></center>

			</form>
		</div>
	</fieldset>
</form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="Formulario Lismat_cod_mat_combo_buscar.js"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$('#pensum').val('');
		$('#cod_doc').val('');
		$('#cod_doc2').val('');
		$('#cod_mat').val(''); 
		$('#lapso').val(''); 		
		$('#seccion').val('');
		

		$.ajax({
			type: "POST",
			url: "getpensum.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

		$('.selector-pensum select').click(function(){
			var v = $(this).val(); 
			$('#pensum').val(v);      
			buscardocente($(".selector-pensum select").val());            
			
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			$('#seccion').val('');
			$(".selector-seccion select").empty();           
		}); 


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
					$(".selector-cod_doc select").html(response).fadeIn();					
				}
			});  
		}

		$('.selector-lapso select').click(function(){
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
			var v = $(this).val();     
			$('#cod_mat').val(v);     
			buscarlapso($(".selector-pensum select").val(),$(".selector-cod_doc select").val(),$(".selector-cod_mat select").val())

			$('#lapso').val('');
			$(".selector-lapso select").empty();
		});   

		function buscarlapso(val1,val2,val3){
			var parametros = {
				"pensum":val1,
				"cod_doc":val2,
				"cod_mat":val3       
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

		$.ajax({
			type: "POST",
			url: "getdocente_x3.php",        
			success: function(response)
			{             					
				$('.selector-cod_doc2 select').html(response).fadeIn();
			}
		});

		$('.selector-cod_doc2 select').change(function() { 
			var v = $(this).val();
			$('#cod_doc2').val(v);		
		});

		$(".selector-lapso select").change(function() { 
			$('#seccion').val('');      
			$(".selector-seccion select").empty();
			buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
		});


		$('.selector-seccion select').click(function(){
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

		$("#actualizar").click(function() {
			actualizar_datos();
		});

		function actualizar_datos(){
			var parametros = {
				"pensum":$('#pensum').val(), 	
				"cod_mat":$('#cod_mat').val(),
				"cod_doc":$('#cod_doc').val(),
				"cod_doc2":$('#cod_doc2').val(),
				"lapso":$('#lapso').val(),																			
				"seccion":$('#seccion').val(),
				"accion":'actualizar'                      
			}          

			$.ajax({
				data:parametros,
				type: "post",
				url: "remplazar_docente.php",				
				success: function(datos)
				{        					
					$('#editable_table').html(datos);
				}
			});
		}   


		$("#Buscar").click(function() {
			enviar_datos();

		});


		function enviar_datos(){
			var parametros = {
				"pensum":$('#pensum').val(), 	
				"cod_mat":$('#cod_mat').val(),
				"cod_doc":$('#cod_doc').val(),	
				"cod_doc2":$('#cod_doc2').val(),			
				"lapso":$('#lapso').val(),					
				"seccion":$('#seccion').val(),
				"accion":'buscar'                      
			}          

			$.ajax({
				data:parametros,
				type: "post",
				url: "remplazar_docente.php",				
				success: function(datos)
				{        
					$('#editable_table').html(datos);
				   
				}
			});
		}   

	});
</script>

</body>
</html>