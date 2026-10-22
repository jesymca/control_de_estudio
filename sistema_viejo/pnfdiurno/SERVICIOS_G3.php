
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
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
$grado=$_POST['grado'];

include 'menu.php';
?>
<html>
<head>
	<style>

	</style>
</head>
<body>
	<div class="container" id="marco" style="width:400px">
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION7.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-print"></span> Listado de Graduados</label>
				</div>
				<p>

					
				       <div class="selector-fecha">        
						<div class="form-group">
							<div class="col-md-12" style="width:100%;margin-left: 20px;margin-top:0px">
								<label for="fe_gr_alu" style="margin-top:20px;">Fecha</label>
								<input type="hidden" id="grado" name="grado" value="<?php echo $grado;?>">
		
								<input id="fe_gr_alu" name="fe_gr_alu" type="hidden" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<div class="selector-fecha" class="col-md-12" style="margin-top:-15px;margin-left:35px">
								<select class="form-control" id="selectfecha" style="background-color:#F0F0F0;width:85%;height: 38px;" required>                     
								</select>
							</div>
						</div>  
					</div>			


					<div class="form-group">
						<div class="col-md-12" style="width: 100%;margin-left:20px;margin-top:12px">
							<input type="submit" class="btn btn-primary" name="submit" value="Siguiente" style="background: #0C4783;width:120px"/> 
							<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
						</div>
					</div>

				</p>

				<table id="editable_table" class="table table-bordered table-striped"></table></center>

			</form>
		</div>
	</fieldset>
</form>






<script type="text/javascript">
	$(document).ready(function() {
			
			$.ajax({				
				type: "POST",
				url: "fecha_select_combo.php",       
				success: function(response)
				{        
					$('.selector-fecha select').html(response).fadeIn();
				}
			});	

			$('.selector-fecha select').change(function(){
	          var v = $(this).val();     
	        $('#fe_gr_alu').val(v);									
}); 	


	});
</script>

</body>
</html>


