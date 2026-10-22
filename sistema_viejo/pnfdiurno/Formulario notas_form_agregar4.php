<?php
session_start();                                                  
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar']==1) {
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

if ($_POST['cedula1']=="") {
	$cedula = $_SESSION['alumno_sec'];
}else{
	$_SESSION['alumno_sec']= $_POST['cedula1'];
	$cedula = $_SESSION['alumno_sec'];
}

require("db.php");
$query = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                      
	$carrera=$row["carrera"];
}
include('/Classes/class_api.php');

$con=new PDF();
$pensum = $con->pensum($carrera);

?>
<html>
<head>
	<style>
		#marco{
			width:800px;
			min-width: 800px;		
			max-width: 800px;		
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
					<form class="form-horizontal" name="informacion" id="effect2" method="post" action="Formulario notas_insertd.php">
						<fieldset>
							<div class="form-group" id="titulo_formulario"> 
								<label id="titulo_formulario">Agregar nota</label>
								<INPUT type="hidden" id="codigo1" name="codigo1" value="<?php echo $cedula;?>">	
								</div>
								<br />
								<table> 
									<tr>
										<td style="width: 20px;"></td>
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
										<td style="width: 20px;"></td> 
										<td style="width: 70px;">
											<div class="form-group">
												<div class="col-md-12" style="width: 150px;margin-top:0px">
													<label for="codigo">Cedula</label>
													<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">											
													<input type="hidden" id="codigo" name="codigo" value="<?php echo $cedula?>">  
													<input id="codigo1" name="codigo1" type="text" placeholder="Codigo" value="<?php echo $cedula?>" class="form-control" style="background-color:#FFFFFF" readonly>
												</div>
											</div>
										</td> 
										<td style="width: 25px;"></td> 
										<td style="width: 180px;"> 
											<div class="form-group">
												<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
													<label for="cod_doc">Docente</label>
													<input type="hidden" id="cod_doc" name="cod_doc">					
												</div>
												<div class="selector-cod_doc">   
													<select style="width:100%;height: 38px;" required></select> 
												</div>
											</div>
										</td>
										<td style="width: 40px;"></td> 
										<td style="width: 350px;">
											<div class="form-group">
												<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
													<label for="cod_mat">Materia</label>  
													<input type="hidden" id="cod_mat" name="cod_mat">					
												</div>
												<div class="selector-cod_mat">   
													<select style="width:100%;height: 38px;" required></select>   
												</div>
											</div>
										</td>						
									</tr>
								</table> 
								<table> 
									<tr> 
										<td style="width: 35px;"></td>
										<td style="width: 70px;"> 
											<div class="form-group">
												<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
													<label for="lapso">Lapso</label>  
													<input type="hidden" id="lapso" name="lapso">					
												</div>
												<div class="selector-lapso">   
													<select style="width:120px;height: 38px;" required></select>  
												</div>
											</div>	
										</td> 
										<td style="width: 40px;"></td>
										<td style="width: 100px;">
											<div class="form-group">
												<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
													<label for="seccion">Seccion</label>
													<input type="hidden" id="seccion" name="seccion">					
												</div>
												<div class="selector-seccion">   
													<select style="width:91%;height: 38px;" required>
														<option value="">Seleccionar</option>
														<?php
														$con->listado_de_secciones($pensum);
														?>
													</select>  
												</div>
											</div> 
										</td>										
										<td style="width: 13px;"></td> 
										<td style="width: 250px;">
											<div class="form-group">
												<div class="col-md-12" style="width: 100%;margin-top:-33px">
													<label for="electiva">Electiva</label>  
													<input type="hidden" id="electiva" name="electiva">
													<div class="selector-electiva">   
														<select style="width:87%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>      
													</div>
												</div>
											</div>
										</td>
										<td style="width: 17px;"></td>
										<td style="width: 100px;">
											<div class="form-group">
												<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px">
													<label for="nota">Nota</label>
													<input type="hidden" id="nota" name="nota">					
												</div>
												<div class="selector-nota">   
													<select style="width:91%;height: 38px;" required></select>														
												</div>
											</div>  
										</td>
										<td style="width: 10px;"></td> 
										<td style="width: 80px;">
											<div class="form-group">
												<div class="col-md-12" style="width: 120px;margin-top:0px">
													<label for="acu">Acu</label>
													<input type="hidden" id="acu" name="acu">  
													<input id="acu1" name="acu1" type="text" placeholder="Acu" class="form-control" style="background-color:#FFFFFF;" readonly>
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
 // $('#marco2').hide();


 buscardocente($('#pensum').val());

 function buscardocente(val1){
 	var parametros = {
 		"pensum":val1,            
 		"opcion":"getdocente",
 		"campos":"pd"
 	}                        	
 	$.ajax({
 		data:parametros,
 		type: "POST",
 		url: "getselect.php",        
 		success: function(response)
 		{
 			$(".selector-cod_doc select").html(response).fadeIn();
 		}
 	});  
 }

 $(".selector-cod_doc select").change(function() {
 	var v = $(this).val();     
 	$('#cod_doc').val(v); 	
 }); 

 $('.selector-cod_mat select').change(function(){
 	var v = $(this).val();     
 	$('#cod_mat').val(v); 	
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