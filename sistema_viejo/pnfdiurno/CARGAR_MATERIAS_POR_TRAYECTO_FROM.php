
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas']==1) {
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
require("db.php");
$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}
$query = "SELECT * FROM alumno WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$cedula=$row["cedula"];
	$carrera=$row["carrera"];
}

include('getpensum3_clase.php');
$con = new carreras();
$pensum = $con->pensum($carrera);

include 'menu.php';
?>
<html>
<head>
</head>
<body>
	<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
	<link rel="stylesheet" type="text/css" href="css/themes/icon.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.easyui.min.js"></script>

	<div class="container" id="marco" style="width: 550px">
		<form class="form-horizontal" id="effect2" method="post">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="fa-magic fa"></span> Generar Materias del Trayecto</label>
				</div>
				<br>
				<table style="margin-left:20px;">
					<tr>
						<td style="width: 120px;">
							<div class="form-group">
								<div class="col-md-12" style="margin-top:0px">
									<label for="cedula">Cedula</label>  
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
									<input id="cedula" name="cedula" type="text" class="form-control" value="<?php echo $cedula?>" readonly style="background-color:#F0F0F0;">
								</div>
							</div>
						</td>
						<td style="width: 5px;"></td> 
						<td style="width: 50px">
							<div class="select_trayecto">
								<div class="form-group">
									<div class="col-md-12" style="margin-left: 0px;margin-top:0px">
										<label for="trayecto">Trayecto</label>  
										<input id="trayecto" name="trayecto" type="hidden" class="form-control">
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top: -15px;margin-left: 0px">
										<select class="form-control" id="selecttrayecto" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td>
						<td style="width: 5px;"></td> 
						<td style="width: 90px;margin-left:10px;"> 
							<div class="selector-lapso">
								<div class="form-group">
									<div class="col-md-12">
										<label for="lapso">Lapso</label>  
										<input id="lapso" name="lapso" type="hidden" placeholder="Lapso" class="form-control" required style="background-color:#F0F0F0;">
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top: -15px;margin-left: 0px">
										<select class="form-control" id="selectlapso" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td> 
						<td style="width: 5px;"></td>
						<td style="width: 70px;">
							<div class="selector-seccion">
								<div class="form-group">
									<div class="col-md-6">
										<label>Seccion</label>
										<input id="seccion" name="seccion" type="hidden" placeholder="Seccion" class="form-control input-md" required style="background-color:#F0F0F0">
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12" style="width: 120px;margin-top: -15px;margin-left: 0px">
										<select class="form-control" id="selecseccion" style="background-color:#F0F0F0">											
										</select>
									</div>
								</div>
							</div>
						</td>
						<td style="width: 5px;"></td> 
						<td style="width: 70px;">							
							<div class="form-group">
								<div class="col-md-12" style="width: 120px;margin-left: 0%;margin-top:0px">
									<label for="cod_doc">Docente</label>  
									<input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control" readonly style="background-color:#F0F0F0;" value="0" readonly>
								</div>
							</div>							
						</td>
					</tr>
				</table> 
				<table style="margin-left:20px;">
					<tr>
					</tr>
					<tr>
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 27px;margin-left: 0px">                                
									<input type="button" class="btn btn-primary" name="generar" id="generar" value="Generar" style="background: #0C4783;width:100px"/> 
									<!--  <a type="button" id="generar" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Generar</a>              -->
								</div>
							</div>
						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-left:0px;margin-top:27px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table>

				<table id="editable_table" class="table table-bordered table-striped"></table>
			</form>
		</div>
	</fieldset>
</form>

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
<script type="text/javascript">
	$(document).ready(function() {   
		hide_loading_message();

		$("#generar").click(function() {
			generar_materias($("#cod_doc").val(),$("#seccion").val(),$("#lapso").val(),$("#trayecto").val(),$("#cedula").val());
		}); 
      

		function generar_materias(val1,val2,val3,val4,val5){
			show_loading_message();
			var parametros = {
				"cod_doc":val1,				
				"seccion":val2,
				"lapso":val3,
				"trayecto":val4,	
				"cedula":val5			
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "CARGAR_MATERIAS_POR_TRAYECTO.php",				
				success: function(response)
				{							
					if(response=="Se agrego la materia"){
                        swal("Se agrego la materia");
					}else{
                       swal("Se agrego la materia");
					}
						
					hide_loading_message();									
				}
			});
		}

		$.ajax({				
			type: "POST",
			url: "getlapso_0.php",				
			success: function(response)
			{							
				$('.selector-lapso select').html(response).fadeIn();
			}
		});

		cargar_trayectos($("#pensum").val(),$("#cedula").val());
		
		function cargar_trayectos(val1,val2){
			show_loading_message();			
			var parametros = {
				"pensum":val1,
				"cedula":val2				
			}
			$.ajax({
				data:parametros,				
				type: "POST",
				url: "gettrayecto.php",				
				success: function(response)
				{   			  
					$('.select_trayecto select').html(response).fadeIn();
					hide_loading_message();
				}
			});
		}

		$('#selectlapso').change(function(){
			var v = $(this).val(); 
			$('#lapso').val(v);
		});

		$.ajax({				
			type: "POST",
			url: "getseccion2f_0.php",				
			success: function(response)
			{
				$('.selector-seccion select').html(response).fadeIn();
			}
		});

		$('#selecseccion').change(function(){
			var v = $(this).val(); 
			$('#seccion').val(v);
		});

		$('#selecttrayecto').change(function(){
			var v = $(this).val(); 
			$('#trayecto').val(v);
		});	

		 // Show loading message
		 function show_loading_message(){
		 	$('#marco').hide();					  	
		 	$('#loading_container').show();
		 }
         // Hide loading message
         function hide_loading_message(){
         	$('#loading_container').hide();
         	$('#marco').show();  	                   
         }

        $('#agregar').click(function(){
			agregar_materia($('#pensum').val(),$('#cod_mat').val(),$('#electiva').val(),$('#lapso').val(),$('#seccion').val(),$('#cedula').val());
		}); 

     });

 </script>
</body>
</html>


