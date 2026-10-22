<?php
include 'menu_web.php';
?>
<table style="margin-left: 10px">
	<tr>
		<td style="width: 25%;" align="left">
			<div class="bs-docs-section" style="margin-left: 1%;margin-top: 20px">
				<form class="form-horizontal" method="post" action="SERVICIOS_B8.php">

					<div class="panel panel-primary" style="width: 100%;">
						<div class="panel-heading">
							<h3 class="panel-title">Historial Academico</h3>
						</div>
						<div class="panel-body">						
							<div class="form-group">
								<div class="col-md-2" style="margin-top:0px">								
									<input name="cedula" type="text" class="form-control" required pattern="[V|E][0-9]{7,9}" style="width: 94px;height:30px;font-size: 12px;z-index: 0;border: 1px inset #E6E6E6;border-radius: 3px" title="Escriba su cedula, ejemplo: V30555222">
								</div>
							</div>

							<div class="col-md-2" style="padding-left:0px;margin-top:-10px">
								<p class="bs-component">
									<input type="submit" class="btn btn-default btn-sm" name="submit" value="Buscar" style="font-size: 11px" />
								</p>
							</div>
						</div>
					</div>			
				</form>

				<form class="form-horizontal" method="post" action="SERVICIOS_B9.php">
					<div class="panel panel-primary" style="width: 100%;">
						<div class="panel-heading">
							<h3 class="panel-title">Constancia de Estudios</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<div class="col-md-12" style="margin-top:0px">

									<input name="cedula" type="text" class="form-control" required pattern="[V|E][0-9]{7,9}" style="width: 94px;height:30px;font-size: 12px;border: 1px inset #E6E6E6;border-radius: 3px" title="Escriba su cedula, ejemplo: V30555222">
								</div>
							</div>

							<div class="col-lg-7" style="padding-left:0px;margin-top:-10px">
								<p class="bs-component">
									<input type="submit" class="btn btn-default btn-sm" name="submit" value="Buscar" style="font-size: 11px" />
								</p>
							</div>
						</div>
					</div>				
				</form>

				<form class="form-horizontal" method="post" action="SERVICIOS_B8.php">
					<div class="panel panel-primary" style="width: 100%;">
						<div class="panel-heading">
							<h3 class="panel-title">Solvencia Academica</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<div class="col-md-12" style="margin-top:0px">
									<input name="cedula" type="text" class="form-control" required pattern="[V|E][0-9]{7,9}" style="width: 94px;height:30px;font-size: 12px;border: 1px inset #E6E6E6;border-radius: 3px" title="Escriba su cedula, ejemplo: V30555222">
								</div>
							</div>



							<div class="col-lg-7" style="padding-left:0px;margin-top:-10px">
								<p class="bs-component">
									<input type="submit" class="btn btn-default btn-sm" name="submit" value="Buscar" style="font-size: 11px" />
								</p>
							</div>
						</div>
					</div>
				</form>		

			</div>
		</td>
		<td style="width: 5%;"></td>
		<td style="width: 60%;">
			<div class="bs-docs-section" style="margin-top: 0px">
				<div class="row">
					<div class="col-lg-4">
						<center><h3>Noticias</h3></center>
						<div class="bs-component">

							<?php
							include('db.php');
							$sql = "SELECT * FROM noticias";
							$resultado = $conn->query($sql);
							if ($resultado->num_rows > 0) { 
								while($fila = $resultado->fetch_assoc()) {
                                
									$coleres="<div class=".chr(34)."panel panel-".$fila["color"].chr(34).">";
									$icono='<span class="'.$fila["icono"].'"></span>';						
									echo $coleres.'
									<div class="panel-heading"><h3 class="panel-title">'.$icono.' '.$fila["titulo"].'</h3></div>
									<div class="panel-body">
									<textarea name="datos" rows="3" cols="60" style="width:100%" readonly>'.$fila["contenido"].'</textarea></div>
									</div>';

							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</td>

</tr>
</table>

<!-- <div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div>

<div class="panel panel-success">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div>

<div class="panel panel-warning">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div>

<div class="panel panel-danger">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div>

<div class="panel panel-info">
	<div class="panel-heading"><h3 class="panel-title">Panel heading</h3></div>
	<div class="panel-body">Panel content</div>
</div> -->
<script type="text/javascript">
	$(document).ready(function() {
		hide_loading_message();

		$("#buscar2").keydown(function(e) {        
			if (e.which == 13){
				e.preventDefault();
				enviar_datos();       
			}
		});
		
		$("#btnbuscar2").click(function() {
			enviar_datos();
		});

		function enviar_datos(){
			show_loading_message();
			var valor = $('#buscar2').val();
			var parametros = {
				"id":valor,
				"usuario":"online"
			}
			$.ajax({
				type: "post",
				url: "buscar2.php",
				data:parametros,
				success: function(datos)
				{        
					$('#editable_table').html(datos);
					hide_loading_message();
				}
			});
		}   
	});
</script>

</body>
</html>