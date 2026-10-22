<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['desactivar_alumnos']==1) {
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
			max-width: 500px;
			min-width: 500px;
		}
	</style> 

</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-list-alt"></span> Desactivar todos los alumnos</label>
				</div>

				<center><table>
					<tr>					
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 27px;margin-left: 0">                                
									<a id="desactivar" class="btn btn-primary" style="width: 140px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Desactivar</a>
								</div>
							</div>
						</td>	

						<td></td>

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:27px">
									<a href="principal.php" class="btn btn-primary" style="width: 140px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table>

				<table id="editable_table" class="table table-bordered table-striped"></table></center>

			</form>
		</div>
	</fieldset>
</form>

<script type="text/javascript">
	$(document).ready(function() {	

		$("#desactivar").click(function() {
			swal({   
				title: "Desea desactivar los alumnos?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#FF292B',
				confirmButtonText: 'Si',
				cancelButtonText: 'No',
				closeOnConfirm: false },
				function(isConfirm){
					if (isConfirm){ 

						$.ajax({
							url: "desactivar_todo.php",
							type: "POST", 			
							success: function(data){
								swal(data,"", "success"); 				
							}
						});	

					} else {
						window.location = 'Desactivar todos los alumnos.php';
					}	
				});
		}); 
	});
</script>

</body>
</html>


