<?php

$cedula=$_POST["cedula"];
$pensum=$_POST["pensum"];
$id=$_POST["id"];
include "db.php";
include 'menu.php';
?>

<div class="container" id="marco" style="width:400px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="Formulario notas_form_agregar.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Grado del alumno</label>
			</div>
			<br>		

			<div class="form-group">
				<div class="col-md-12" style="width:150px;margin-left: 20px;">
					<label for="grado">Grado</label>
					<input id="id" name="id" type="hidden" value="<?php echo $id?>">
					<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">
					<input id="cedula" name="cedula" type="hidden" value="<?php echo $cedula?>">
					<input id="grado" name="grado" type="hidden" class="form-control" required>
				</div>

				<div class="selector-grado">   
					<select style="width:150px;margin-top:26px;height: 38px;margin-left: -135px;"></select>      
				</div>
			</div>       

			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<input type="submit" id="submit" class="btn btn-primary" name="submit" id="submit" value="Siguiente" style="width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>
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
<script>
	$(document).ready(function(){  

		hide_loading_message();
		$('#submit').click(function(){
			show_loading_message();    
		});

		$('#grado').val('');
		$('.selector-grado select').change(function(){
			var v = $(this).val(); 
			$('#grado').val(v);       
		});

		var parametros = {
			"pensum":$('#pensum').val(),
			"opcion":"getgrado"                   		                 
		}
		$.ajax({
			data:parametros,
			type: "POST",
			url: "getselect.php",
			success: function(response)
			{
				$('.selector-grado select').html(response).fadeIn();
			}
		}); 

		// Show loading message
		function show_loading_message(){
			$('#loading_container').show();
            $('#marco').hide();
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


