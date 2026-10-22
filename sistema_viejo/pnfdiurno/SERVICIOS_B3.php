<?php

$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}

include "db.php";

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) {  
		$cedula=$fila["cedula"];		
	}
}

include 'menu.php';

?>

<div class="container" id="marco" style="width:400px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_index2.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Materia a mostrar</label>
			</div>
			<br>		

			<table>
				<tr>   
					<td style="width: 150px;">

						<div class="form-group">
							<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: 0px">
								<label for="cod_mat" style="margin-left: 15px;margin-top:-35px;">Materia</label>  
								<input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>"> 
								<input type="hidden" id="id" name="id" value="<?php echo $id;?>"> 
								<input type="hidden" id="cod_mat" name="cod_mat">         
							</div>

							<div class="selector-cod_mat">   
								<select style="width:100px;height: 38px;margin-top:25px;" required></select>   
							</div>
						</div>
					</td>  

					<td style="width: 20px;"></td>
					<td style="width: 350px;">

						<div class="form-group">
							<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
								<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Mostrar" style="background: #0C4783;width:120px"/> 
								<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<script>
	$(document).ready(function(){  

		var parametros = {
			"cedula":$('#cedula').val()                
		}

		$.ajax({
			data:parametros,
			type: "POST",
			url: "getcod_mat.php",
			success: function(response)
			{
				$('.selector-cod_mat select').html(response).fadeIn();
			}
		}); 


		$('.selector-cod_mat select').change(function(){
			var v = $(this).val(); 
			$('#cod_mat').val(v);			
		});  

      

	});
</script>
</body>
</html>

