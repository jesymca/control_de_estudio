
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
<html>
<head>

	<style>
		#marco
		{      
			max-width: 620px;
			min-width: 220px;
		}
		.col-md-12 {
			padding-left:30px; 
			padding-right:30px;
			min-width: 205px;
		}
		.selector-docente,.selector-seccion,.selector-lapso,.selector-tipolapso{
			padding-left:30px; 
			padding-right:30px;
			min-width: 350px;
		}

	</style>

	<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
	<link rel="stylesheet" type="text/css" href="css/themes/icon.css">


	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.easyui.min.js"></script>



</head>
<body>
	<div class="form-group" id="marco">
		<form class="form-horizontal" id="effect2" method="post" action="ACTA DE CALIFICACION FINAL.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-book"></span> Acta de calificacion</label>
				</div>

				<br>
				         <div class="row">
                         <div class="col-md-12">

				
                            <p>					
							<div class="form-group">
								<div class="col-md-12" style="width:50%;">
									<label for="cod_doc">Docente</label>  
									<input id="cod_doc" name="cod_doc" type="text" class="form-control" required>
								</div>
							
							
				
							<div class="selector-docente" id="sp1">   
								<select style="width:50%;height: 38px;margin-top:23px"></select>      
							</div>
							</div>
                            </p>	  



							</div>
							</div>
			                </p>
				            <p>
							<div class="form-group">
								<div class="col-md-12">
									<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="background: #0C4783;width:120px"/> 
								    <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
													
								
					        </div>
			                </p>


		</form>
	</div>
</fieldset>
</form>

<script>
	$(document).ready(function(){     

		$.ajax({
			type: "POST",
			url: "getdocente.php",
			success: function(response)
			{
				$('.selector-docente select').html(response).fadeIn();
			}
		});

		$('#sp1 select').click(function(){
			var v = $(this).val(); 
			$('#cod_doc').val(v); 			
		}); 

        

	});
</script>

</body>
</html>
