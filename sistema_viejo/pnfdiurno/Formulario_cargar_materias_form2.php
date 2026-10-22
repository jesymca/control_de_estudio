<?php
include 'db.php';
include 'menu.php';
?>


<div class="container" id="marco" style="width:780px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="Formulario notas_insertb_x2.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Inscribir varias materias a un alumno</label>
			</div>
            <br>

			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">
					<label for="pensum">Pensum</label>
					<input type="hidden" id="pensum" name="pensum"> 					
				</div>

				<div class="selector-pensum">   
					<select style="width:20%;height: 38px;margin-top:26px;margin-left: -15px;"></select>      
				</div>
			</div>

            <div class="form-group">
				<div class="col-md-12" style="width:0px;margin-left: 20px;">
					<label for="alumno">Alumno </label>  
					<input id="alumno" name="alumno" type="hidden" class="form-control" required>
				</div>

				<div class="selector-alumno">   
					<select style="width:70%;margin-top:26px;height: 38px;margin-left: -15px;"></select>      
				</div>
			</div>  

			<div class="form-group">
				<div class="col-md-12" style="width:24%;margin-left: 20px;margin-top:0px;">
					<label for="cod_doc">Docente</label>
					<input type="text" class="form-control" id="cod_doc" name="cod_doc" value="0" readonly style="background: white">					
				</div>				
			</div>

			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">
					<label for="cod_mat">Materia</label>  
				</div>

				<div class="selector-cod_mat" id="sp5">   
					<select required name="cod_mat[]" multiple="true" style="width:91%;height: 300px;margin-top:26px;margin-left: -15px;"></select>   
				</div>
			</div>


			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">
					<label for="lapso">Lapso</label>  
					<input type="hidden" id="lapso" name="lapso">					
				</div>

				<div class="selector-lapso" id="sp2">   
					<select style="width:20%;height: 38px;margin-top:26px;margin-left: -15px;"></select>  
				</div>
			</div>


			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">
					<label for="seccion">Seccion</label>
					<input type="hidden" id="seccion" name="seccion">					
				</div>

			<div class="selector-seccion" id="sp6">   
					<select style="width:20%;height: 38px;margin-top:26px;margin-left: -15px;"></select>    
				</div>
			</div>    

			<br>


			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Salir</a>
				</div>
			</div>

		</fieldset>
	</form>
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

		$('.selector-pensum select').change(function(){
			var v = $(this).val(); 
			$('#pensum').val(v);			
			$('#alumno').val('');
			$(".selector-alumno select").empty();	
			$('#cod_mat').val('');
			$(".selector-cod_mat select").empty();				
			buscaralumno($(".selector-pensum select").val());		
			buscarmateria($(".selector-pensum select").val());            
	
		}); 

        $.ajax({				
				type: "POST",
				url: "getdocente.php",        
				success: function(response)
				{
					$(".selector-docente select").html(response).fadeIn();
				}
			});  
         

		function buscaralumno(val1){
			var parametros = {
				"pensum":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getalumno_x.php",        
				success: function(response)
				{
					$('.selector-alumno select').html(response).fadeIn();	

				}
			});  
		}

		function buscarmateria(val1){			
			var parametros = {
				"pensum":val1
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getseccion_x2.php",        
				success: function(response)
				{
					$('.selector-cod_mat select').html(response).fadeIn();
				}
			});
		}


		$('.selector-alumno select').change(function(){
			var v = $(this).val(); 
			$('#alumno').val(v);       
		}); 


		$('#sp1 select').change(function(){
			var v = $(this).val(); 
			$('#cod_doc').val(v);       
		}); 


		$('#sp2 select').change(function(){
			var v = $(this).val();     
			$('#lapso').val(v); 		

		}); 


		$.ajax({				
			type: "POST",
			url: "getlapso_0.php",        
			success: function(response)
			{             
				$('.selector-lapso select').html(response).fadeIn();
			}
		});



		$('#sp6 select').change(function(){
			var v = $(this).val();     
			$('#seccion').val(v);         

		}); 


			$.ajax({				
				type: "POST",
				url: "getseccion2f_0.php",        
				success: function(response)
				{
					$('.selector-seccion select').html(response).fadeIn();
				}
			});


	});
</script>

</body>
</html>


