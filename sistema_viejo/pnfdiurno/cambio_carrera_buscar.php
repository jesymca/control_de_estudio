
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

$usuario=$_SESSION['username'];

include 'menu.php';

?>
<html>
<head>



</head>
<body>
	<div class="container" id="marco" style="width:70%">
		<form class="form-horizontal" id="effect2" method="post">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<input type="hidden" id="usuario" name="usuario" value="<?php echo $usuario;?>">
					<label id="titulo_formulario"><span class="glyphicon glyphicon-retweet"></span> Reporte de Cambios de carrera</label>
				</div>

				<center><table>
					<tr>
						<td>
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
									<label for="buscar">Buscar</label>  
									<input id="buscar2" name="buscar2" type="text" placeholder="buscar" class="form-control">
								</div>
							</div>
						</td>
                  					
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 27px;margin-left: 0">                                
									<a type="button" id="btnbuscar2" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Buscar</a>             
								</div>
							</div>
						</td>
						
						
						<td></td>

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:27px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
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

<script src="jquery-3.1.1.min3.js" type="text/javascript"></script>
<script>
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

	  $.ajax({
      type: "POST",
      url: "getpensum.php",
      success: function(response)
      {
        $('.selector-pensum select').html(response).fadeIn();
      }
    });

 
		
		function enviar_datos(){
			var valor = $('#buscar2').val();
			var valor2 = $('#buscar2').val();			
			var valor3 = $('#usuario').val();
			$.ajax({
				type: "post",
				url: "rbuscar cambiar.php",
				data:{buscar:valor,cedula:valor2,usuario:valor3},
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


