
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_modificar']==1) {
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
include 'db.php';
include 'menu.php';
$id = intval($_GET['id']);

$sql = "SELECT * FROM notas WHERE id='".$id."'";
$resultado = $conn->query($sql);
while($fila = $resultado->fetch_assoc()) {
	$id=$fila["id"];
	$cedula=$fila["codigo"];
	$cod_mat=$fila["cod_mat"];
	$carrera = $fila["carrera"];
	$seccion=$fila["seccion"];
	$nota=$fila["nota"];
	$acu=$fila["acu"];
	$lapso=$fila["lapso"];	
	$cod_doc=$fila["cod_doc"];
	$cod_usu_ant = $fila['cod_usu'];	
	$electiva = $fila["electiva"];
	$fecha_ant = $fila["fecha"];
}

$conn->close();



?>
<html>
<head>
	<style>
		#marco{
			width:870px;
			min-width: 870px;		
			max-width: 870px;		
		}
		#titulo_formulario{
			width:100%;
		}


	</style>
</head>
<body>
	<center><table>
		<tr>
			<td>				
				<div class="container" id="marco" style="background: #dcdcdc">                                                       
					<form class="form-horizontal" name="informacion" id="edit2" method="post" action="edit2.php">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario">Cambiar carrera</label>								
								</div>
								<br />
								<table> 
									<tr>
										<td style="width: 10px;"></td>
										<td style="width: 250px;">
											<div class="form-group">
												<div class="col-md-12">
													<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="background: #0C4783;width:120px"/> 
													<a href="cambiar carrera.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
												</div>
											</div>
										</td>
									</tr>
	<table class="responstable">
            <thead>
              <tr>
                 
                <th width="1%">Codigo</th>                       
                <th width="5%">Cedula</th>
                <th width="35%">Nombre</th>                
                <th width="15%">Pensum</th>                                               
              </tr>
        
      </thead>
      
								</table> 
								<table> 
									<tr>
										<td style="width: 10px;"></td> 
										<td style="width: 90px;">
											<div class="form-group">
												<div class="col-md-2" style="width:90px;margin-top:0px">
													<!-- <label for="codigo">Codigo</label> readonly-->
				                                    <input type="text" id="codigo" name="codigo" class="form-control" value="<?php echo $codigo?>" style="background: #ffffff";> 
				                                </div>
											</div>
										</td>

									 
										<td style="width: 122px;">
											<div class="form-group">
												<div class="col-md-6" style="width:122px;margin-top:0px;margin-left:+0px;">
													<!-- <label for="cedula">Cedula</label> -->
				                                    <input type="text" id="cedula" name="cedula" class="form-control" value="<?php echo $cedula?>">  												</div>
											</div>
										</td> 
										
                                      
										<td style="width: 490px;">
											<div class="form-group">
												<div class="col-md-2" style="width:490px;margin-top:0px;margin-left:-10px;">
													<!-- <label for="nombre">Nombre</label> class="form-control"-->
				                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $nombre?>">  												</div>
											</div>
										</td> 
                                        

                                       
										<td style="width: 80px;">
											<div class="form-group">
												<div class="col-md-6" style="width:80px;margin-top:0px;margin-left:-10px;">
													<!-- <label for="pensum">Pensum</label> -->
				                                    <input type="text" id="pensum" name="pensum" class="form-control" value="<?php echo $pensum?>"">  												</div>
											</div>
										</td> 

										<td style="width: 10px;"></td>										
										<td style="width: 140px;">
											<div class="form-group">											
												<div class="selector-pensum" style="width:140px;margin-top:0px;margin-left:-10px;"> 
													<select class="form-control">	
														<option value="">Pensum</option>														
													</select>  
												</div>
											</div> 
										</td>
										
									</table>
							</fieldset>
						</form>
					</div>
				</td>
			</table></center>	
</table>
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

	$.ajax({
			type: "POST",
			url: "getpensum.php",
			success: function(response)
			{
				$('.selector-pensum select').html(response).fadeIn();
			}
		});

 $(".selector-pensum select").change(function() {
 	var inicio = $(this).val();
  //var v = $(this).val();
  var v = inicio.substring(0,1); 
 	  
 	$('#pensum').val(v); 		
 }); 


 buscarmateria($('#pensum').val());

 function buscarmateria(val1){
   //show_loading_message();
   var parametros = {
   	"pensum":val1							
   }							
   $.ajax({
   	data:parametros,
   	type: "POST",
   	url: "getmateria.php",        
   	success: function(response)
   	{									
   		$('.selector-cod_mat select').html(response).fadeIn();
	   //hide_loading_message();
	}
});
}

function show_loading_message(){
	$('#marco').hide();					  	
	$('#loading_container').show();
}

function hide_loading_message(){
	$('#loading_container').hide();
	$('#marco').show();  	                   
}

});
</script>

</body>
</html>		