
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
			max-width: 570px;
		}
		.col-md-12 {
			padding-left:30px; 
			padding-right:30px;
			min-width: 205px;
		}
		.selector-docente,.selector-cod_mat,.selector-lapso,.selector-tipolapso{
			padding-left:30px; 
			padding-right:30px;
			min-width: 350px;
		}
		.panel {
			overflow-y: hidden;
			overflow-x: hidden;
			-ms-overflow-style: hidden;
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
		<form class="form-horizontal" id="effect2" method="post" action="listado_de_comedor.php">
			<fieldset>
				<div id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="fa fa-cutlery"></span> Listado a Comedor</label>
				</div>
				<br>
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 0px;margin-top:0px">
						<label for="lapso">Lapso</label>     
						<input type="hidden" id="lapso" name="lapso"> 
						<div class="selector-lapso">   
							<select style="width:82%;height: 38px;margin-top:0px;margin-left: -30px;position: absolute;" required></select>   
						</div> 
					</div>
				</div>
				<br><br>
				<div class="form-group">
					<div class="col-md-12" style="width:50%;">
						<label for="url">Nombre de Archivo</label>  
						<input id="url" name="url" type="text" class="form-control" required value="Listado a Comedor">
					</div>							
				</div>	
				<br>			
				<div class="form-group">
					<div class="col-md-12"> 
						<input type="submit" class="btn btn-primary" name="submit" value="Exportar a Excel" style="background: #0C4783;width:180px"/> 

					</div>
				</div>
				<br>
				<br>
				
			</fieldset>		
		</form>
		<div class="form-group" style="padding-left:20px;padding-right:15px;">

			<form class="form-horizontal" method="post" action="LISTADO DE ALUMNOS SECCION.php">		
				<input type="hidden" id="carrera1" name="carrera1">
				<input type="hidden" id="seccion1" name="seccion1">            
				<input type="hidden" id="lapso1" name="lapso1">
			</form>
		</div>	
	</div>			

	<script>
		$(document).ready(function(){     

			$.ajax({							
				type: "POST",
				url: "getlapso_0.php",				
				success: function(response)
				{
					$('.selector-lapso select').html(response).fadeIn();
				}
			});

    $('.selector-lapso select').change(function(){
      var v = $(this).val(); 
      $('#url').val("Listado a Comedor " + v);       
    }); 



		});
	</script>

</body>
</html>
