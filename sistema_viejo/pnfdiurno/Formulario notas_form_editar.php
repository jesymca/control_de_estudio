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

include('/Classes/class_api.php');
$con=new PDF();
$pensum = $con->pensum($carrera);


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
				<div class="container" id="marco">                                                       
					<form class="form-horizontal" name="informacion" id="effect2" method="post" action="Formulario notas_update.php">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario">Modificar nota</label>								
								</div>
								<br />
								<table> 
									<tr>
										<td style="width: 10px;"></td>
										<td style="width: 250px;">
											<div class="form-group">
												<div class="col-md-12">
													<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="background: #0C4783;width:120px"/> 
													<a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
												</div>
											</div>
										</td>
									</tr>
								</table> 
								<table> 
									<tr>
										<td style="width: 10px;"></td> 
										<td style="width: 100px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px">
													<label for="cedula">Cedula</label>
													<input type="hidden" id="id" name="id" value="<?php echo $id?>">
													<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">											
													<input type="hidden" id="cod_usu_ant" name="cod_usu_ant" value="<?php echo $cod_usu_ant?>">	
													<input type="hidden" id="fecha_ant" name="fecha_ant" value="<?php echo $fecha_ant?>">	
													<input type="hidden" id="carrera" name="carrera" value="<?php echo $carrera?>">														
													<input type="text" id="cedula" name="cedula" class="form-control" value="<?php echo $cedula?>" style="background: #FFFFFF;" readonly required>  												</div>
											</div>
										</td> 
										<td style="width: 10px;"></td> 
										<td style="width: 230px;"> 
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="cod_doc">Docente</label>
													<input type="hidden" id="cod_doc_ant" name="cod_doc_ant" value="<?php echo $cod_doc?>">
													<input type="text" id="cod_doc" class="form-control" name="cod_doc" style="background: #FFFFFF;" value="<?php echo $cod_doc?>" readonly required>					
												</div>
												<div class="selector-cod_doc">   
													<select style="width:120px;height: 37px;margin-top:28px"></select> 
												</div>
											</div>
										</td>
										<td style="width: 10px;"></td> 
										<td style="width: 230px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="cod_mat">Materia</label>  
													<input type="hidden" id="cod_mat_ant" name="cod_mat_ant" value="<?php echo $cod_mat?>">
													<input type="text" id="cod_mat" class="form-control" name="cod_mat" style="background: #FFFFFF;" value="<?php echo $cod_mat?>" readonly required>
												</div>
												<div class="selector-cod_mat">   
													<select style="width:120px;height: 37px;margin-top:28px"></select>   
												</div>
											</div>
										</td>	

										<td style="width: 10px;"></td> 
										<td style="width: 230px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px">
													<label for="electiva">Electiva</label>  
													<input type="hidden" id="electiva_ant" name="electiva_ant" value="<?php echo $electiva?>">
													<input type="text" id="electiva" class="form-control" name="electiva" style="background: #FFFFFF;" value="<?php echo $electiva?>" readonly>
												</div>
                                                <div class="selector-electiva">   
													<select style="width:120px;height: 37px;margin-top:28px"></select>   
												</div>
											</div>
										</td>

									</tr>
								</table> 
								<table> 
									<tr> 
									    <td style="width: 10px;"></td>										
										<td style="width: 230px;"> 
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="lapso">Lapso</label>  
													<input type="hidden" id="lapso_ant" name="lapso_ant" value="<?php echo $lapso?>">
													<input type="text" id="lapso" class="form-control" name="lapso" style="background: #FFFFFF;" value="<?php echo $lapso?>" readonly required>					
												</div>
												<div class="selector-lapso">   
													<select style="width:120px;height: 37px;margin-top:28px"></select>  
												</div>									
											</div>	
										</td>
                                        <td style="width: 10px;"></td>
										<td style="width: 230px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="seccion">Seccion</label>
													<input type="hidden" id="seccion_ant" name="seccion_ant" value="<?php echo $seccion?>">
													<input type="text" id="seccion" class="form-control" name="seccion" style="background: #FFFFFF;" value="<?php echo $seccion?>" readonly required>					
												</div>
												<div class="selector-seccion">   
													<select style="width:120px;height: 37px;margin-top:28px;">
														<option value="">Seleccionar</option>
														<?php
														$con->listado_de_secciones($pensum);
														?>
													</select>  
												</div>
											</div> 
										</td>										
										
										<td style="width: 10px;"></td>
										<td style="width: 230px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px;margin-left: 0px">
													<label for="nota">Nota</label>
													<input type="hidden" id="nota_ant" name="nota_ant" value="<?php echo $nota?>">
													<input type="text" id="nota" class="form-control" name="nota" style="background: #FFFFFF;" value="<?php echo $nota?>" readonly required>					
												</div>
												<div class="selector-nota">   
													<select style="width:120px;height: 37px;margin-top:28px;"></select>														
												</div>
											</div>  
										</td>
										<td style="width: 10px;"></td> 
										<td style="width: 100px;">
											<div class="form-group">
												<div class="col-md-12" style="width:120px;margin-top:0px">
													<label for="acu">Acu</label>
													<input type="hidden" id="acu_ant" name="acu_ant" value="<?php echo $acu?>">
													<input type="text" id="acu" class="form-control" name="acu" style="background: #FFFFFF;" value="<?php echo $acu?>" readonly>  
												</div>
											</div>
										</td>
						
										</tr>
								</table>
							</fieldset>
						</form>
					</div>
				</td>
			</table></center>	
			<center><table>
				<tr>
					<td>
						<div class="form-group" id="marco2">
							<IMG SRC="pnf.jpg" WIDTH="200" HEIGHT="500">
							</div>
						</td>
					</tr>
				</table></center>

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
 	url: "getdocente_x4.php",
 	success: function(response)
 	{
 		$('.selector-cod_doc select').html(response).fadeIn();
 	}
 });

 $(".selector-cod_doc select").change(function() {
 	var v = $(this).val();     
 	$('#cod_doc').val(v); 		
 }); 

 $('.selector-cod_mat select').change(function(){
 	var v = $(this).val();     
 	$('#cod_mat').val(v); 	
    $('#electiva').val('');
    $(".selector-electiva select").empty();	 	
 	buscarelectiva($("#pensum").val(),$('.selector-cod_mat select').val()); 					
 }); 

 $('.selector-lapso select').change(function(){
 	var v = $(this).val();     
 	$('#lapso').val(v); 	
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


$.ajax({								
	type: "POST",
	url: "getlapso_0.php",        
	success: function(response)
	{             
		$('.selector-lapso select').html(response).fadeIn();
	}
});


$('.selector-seccion select').change(function(){
	var v = $(this).val();     
	$('#seccion').val(v);									
}); 


function buscarelectiva(val1,val2){
	var parametros = {
		"pensum":val1,
		"cod_mat":val2
	}
	$.ajax({
		data:parametros,
		type: "POST",
		url: "getelectivas.php",        
		success: function(response)
		{
			$('.selector-electiva select').html(response).fadeIn();
		}
	});
}

$('.selector-electiva select').change(function(){
	var v = $(this).val(); 
	$('#electiva').val(v);	
});

$(".selector-nota select").change(function() {       
	var v = $(this).val(); 
	$('#nota').val(v);
	var nota = $("#nota").val();
	if(nota>20){
		swal("Nota invalida");                
	}
	else{
		calcular(nota);
	}
});

function calcular(valor) {     
	total= parseInt(valor)*5; 
	if(total>=0){
		document.getElementById('acu').value=total;
		document.getElementById('acu1').value=total;
	}else{
		document.getElementById('acu').value=0; 
		document.getElementById('acu1').value=0; 
	}

}

$.ajax({
	type: "POST",
	url: "Formulario Nota_nota_select_combo.php",
	success: function(response)
	{
		$('.selector-nota select').html(response).fadeIn();
	}
});


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