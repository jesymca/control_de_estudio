
<?php
session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['cambiar_materia']==1) {
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
              	width:90%;
              	height: 38px;
              	margin-top:0px;              	
              	margin-right: 30%;
              }      
        }

        @media (min-width: 993px) {            

              select{
              	width:95%;
              	height: 38px;
              	margin-top:0px;              
              	margin-right: 5%;
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
					<label id="titulo_formulario"><span class="glyphicon glyphicon-hand-down"></span> Cambiar Electiva</label>
				</div>


				<p> 
					<div class="form-group">
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="pensum">Pensum</label>
							<input type="hidden" id="pensum" name="pensum">                     
						</div>

						<div class="selector-pensum" style="margin-left: 25px;">   
							<select></select>      
						</div>
					</div>



					<div class="form-group">
						
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="cod_doc">Docente</label>
							<input type="hidden" id="cod_doc" name="cod_doc">                   
						</div>

						<div class="selector-docente" style="margin-left: 25px;">   
							<select></select> 
						</div>
					</div>



					<div class="form-group">
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="cod_mat">Materia</label>  
							<input type="hidden" id="cod_mat" name="cod_mat">                   
						</div>

						<div class="selector-cod_mat" style="margin-left: 25px;">   
							<select></select>   
						</div>
					</div>

		

					<div class="form-group">
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="electiva2">Electiva</label>  
							<input type="hidden" id="electiva" name="electiva2">                   
						</div>

						<div class="selector-electiva" style="margin-left: 25px;">   
							<select></select>   
						</div>
					</div>


					<div class="form-group">
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="lapso">Lapso</label>  
							<input type="hidden" id="lapso" name="lapso">                   
						</div>

						<div class="selector-lapso" style="margin-left: 25px;">   
							<select></select>  
						</div>
					</div>



					<div class="form-group">
						<div class="col-md-12" style="margin-left: 10px;">
							<label for="seccion">Seccion</label>
							<input type="hidden" id="seccion" name="seccion">                   
						</div>


						<div class="selector-seccion" style="margin-left: 25px;">   
							<select></select>  
						</div>
					</div> 

					
				</p>

				<br />


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
			buscarmateria2($(".selector-pensum select").val());           
			$('#cod_doc').val('');
			$(".selector-cod_doc select").empty();
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
					$(".selector-docente select").html(response).fadeIn();					
				}
			});  
		}

		
		$('.selector-lapso select').click(function(){
			var v = $(this).val();     
			$('#lapso').val(v); 
			buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
		}); 


		$(".selector-docente select").change(function() {
			var v = $(this).val();     
			$('#cod_doc').val(v);
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();
			$('#lapso').val('');
			$(".selector-lapso select").empty();
			buscarmateria($(".selector-pensum select").val(),$(".selector-docente select").val());
			
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


        function buscarmateria2(val1){
			var parametros = {
				"pensum":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getseccion_x4.php",        
				success: function(response)
				{
					$('.selector-cod_mat2 select').html(response).fadeIn();
					
				}
			});
		}


		$(".selector-cod_mat select").change(function() {  
			var v = $(this).val();     
			$('#cod_mat').val(v);     
			buscarlapso($(".selector-pensum select").val(),$(".selector-docente select").val(),$(".selector-cod_mat select").val())
            buscarelectiva($(".selector-pensum select").val(),$('.selector-cod_mat select').val())     
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




		$(".selector-cod_mat2 select").change(function() { 
			var v = $(this).val();
			$('#cod_mat2').val(v);			
		});

		$(".selector-lapso select").change(function() { 
			$('#seccion').val('');      
			$(".selector-seccion select").empty();
			buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
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
				"electiva":$('#electiva').val(),				
				"cod_doc":$('#cod_doc').val(),
				"lapso":$('#lapso').val(),									
				"seccion":$('#seccion').val(),				
				"accion":'actualizar'                      
			}          

			$.ajax({
				data:parametros,
				type: "post",
				url: "remplazar_electiva.php",				
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
				"lapso":$('#lapso').val(),					
				"seccion":$('#seccion').val(),
				"accion":'buscar'                      
			}          

			$.ajax({
				data:parametros,
				type: "post",
				url: "remplazar_electiva.php",				
				success: function(datos)
				{        
					$('#editable_table').html(datos);
				}
			});
		}   


	  function buscarelectiva(val1,val2){
      var parametros = {
        "pensum":val1,
        "cod_mat":val2
      }
      $.ajax({
        data:parametros,
        type: "POST",
        url: "getelectivas.php",        
        success: function(response)
        {
          
          $('.selector-electiva select').html(response).fadeIn();
        }
      });
    }


    $('.selector-electiva select').click(function(){
      var v = $(this).val(); 
      $('#electiva').val(v);     
    }); 



	});
</script>

</body>
</html>