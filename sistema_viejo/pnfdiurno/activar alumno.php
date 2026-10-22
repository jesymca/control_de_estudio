
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
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

$id = intval($_GET['id']);
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
<html>
<head>


	<style>
		#marco
		{    
			
			max-width: 700px;
			min-width: 700px;
		}

	</style> 

	
</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-list-alt"></span> Activar o Desactivar alumno</label>
				</div>

				<center><table>
					<tr>
						<td>
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
									<label for="buscar">Buscar</label>  
									<input id="buscar2" name="buscar2" type="text" placeholder="buscar" class="form-control"  value="<?php echo $cedula?>">
								</div>
							</div>
						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 27px;margin-left: 0">                                
									<a type="button" id="btnbuscar2" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</a>             
								</div>
							</div>
						</td>
						
						
						<td></td>

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:27px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
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

		$("#btnbuscar2").click(function() {
			enviar_datos();
		});

	    $("#buscar2").keydown(function(e) {        
	      if (e.which == 13){
	        e.preventDefault();
	        enviar_datos();       
	      }
	    });
		
		function enviar_datos(){
			var valor = $('#buscar2').val();
			$.ajax({
				type: "post",
				url: "buscar activar.php",
				data:{buscar:valor},
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


