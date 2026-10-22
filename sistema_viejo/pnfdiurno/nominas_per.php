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

<div class="container" id="marco" style="width: 400px">
	<form class="form-horizontal" id="effect2" method="post" action="LISTADO DE PER.php">
		<fieldset>
			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="fa fa-shopping-bag"></span> Listado a Per por Materia</label>
			</div>           

			<p> 

				<div class="form-group" style="padding-bottom:30px;">
					<div class="col-md-12" style="margin-left: 20px;margin-top:0px">
						<label for="pensum">Pensum</label>
						<input type="hidden" id="pensum" name="pensum">                     
						<div class="selector-pensum">   
							<select style="width:82%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
						</div>
					</div>
				</div>

				<div class="form-group" style="padding-bottom:30px;">
					<div class="col-md-12" style="margin-left: 20px;margin-top:0px">
						<label for="cod_mat">Materia</label>  
						<input type="hidden" id="cod_mat" name="cod_mat">                   
						<div class="selector-cod_mat">   
							<select style="width:82%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
						</div>
					</div>
				</div>

				<div class="form-group" style="padding-bottom:30px;">
					<div class="col-md-12" style="margin-left: 20px;margin-top:0px">
						<label for="lapso">Año</label>  
						<input type="hidden" id="lapso" name="lapso">                   

						<div class="selector-lapso">   
							<select style="width:82%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
						</div>
					</div>
				</div>
		</p>


		<div class="form-group" style="padding-left: 20px;padding-top:10px;">
			<div class="col-md-12">
				<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="width:120px"/> 
				<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
			</div>
		</div>          

	</fieldset>
</form>

</div>



</body>
</html>

<script>
	$(document).ready(function(){        
        
		$('#pensum').val('');		
		$('#cod_mat').val(''); 

		$.ajax({
			type: "POST",
			url: "getpensum.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

		$('.selector-pensum select').change(function(){
			var v = $(this).val(); 
			$('#pensum').val(v); 			
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();			
			buscarmateria($(".selector-pensum select").val());					
            buscarlapso($(".selector-pensum select").val());           
		    // buscar_grado($(".selector-pensum select").val());
		}); 		

		function buscarmateria(val1){
			var parametros = {
				"pensum":val1				
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getseccion_x5.php",        
				success: function(response)
				{
					$('.selector-cod_mat select').html(response).fadeIn();
				}
			});
		}

		$(".selector-cod_mat select").change(function() {  
			var v = $(this).val();     
			$('#cod_mat').val(v); 	
		}); 

		$('.selector-lapso select').change(function(){
			var v = $(this).val();     
			$('#lapso').val(v); 			
		}); 

		// $('.selector-grado select').change(function(){
		// 	var v = $(this).val();     
		// 	$('#grado').val(v); 			
		// }); 

		function buscarlapso(val1){
			var parametros = {
				"pensum":val1				
			}
		$.ajax({
			data:parametros,				
			type: "POST",
			url: "getlapso_5.php",        
			success: function(response)
			{             
				$('.selector-lapso select').html(response).fadeIn();
			}
		});
        }

  //       function buscar_grado(val1){
		// 	var parametros = {
		// 		"pensum":val1				
		// 	}
		// $.ajax({
		// 	data:parametros,				
		// 	type: "POST",
		// 	url: "getpensum4.php",        
		// 	success: function(response)
		// 	{             
		// 		$('.selector-grado select').html(response).fadeIn();
		// 	}
		// });
  //       }
		

	});
</script>
