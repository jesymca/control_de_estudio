<?php
include 'menu.php';

include "db.php";
$id = intval($_GET['id']);

$sql = "SELECT * FROM alumno WHERE id='".$id."'";        
$resultado = $conn->query($sql);
if ($resultado->num_rows > 0) {               
	while($fila = $resultado->fetch_assoc()) { 
		$cedula=$fila["cedula"];
		$carrera=$fila["carrera"];		
	}
}

include('/Classes/class_api.php');
$con=new PDF();
$pensum = $con->pensum($carrera);



?>
<div class="container" id="marco" style="width:400px;">
	<form class="form-horizontal" id="effect2" method="post" action="Formulario notas_insertb_x3.php"><!-- Formulario notas_insertb.php -->
		<fieldset>
			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-floppy-disk"></span> Agregar materia</label>
			</div>
			<p>
				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="margin-left: 20px;margin-top:0px">
						<label for="edula">Cedula</label>  
						<input type="Text" id="cedula" name="cedula" value="<?php echo $cedula;?>" class="form-control" readonly style="background: white;width: 86%;">
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:-10px">
						<label for="cod_doc">Cod Doc</label> 
						
						<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">     
						<input type="hidden" id="cod_doc" name="cod_doc"> 
						<div class="selector-cod_doc">   
							<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select> 
						</div>
					</div>
				</div>
				<br>


				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
						<label for="cod_mat">Cod Mat</label>  
						<input type="hidden" id="cod_mat" name="cod_mat">
						<div class="selector-cod_mat">    
							<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>     
						</div>
					</div>
				</div>
				<br>
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
						<label for="electiva">Electiva</label>  
						<input type="hidden" id="electiva" name="electiva">
						<div class="selector-electiva">   
							<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;"></select>     
						</div>
					</div>
				</div>
				<br>
				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
						<label for="lapso">Lapso</label>     
						<input type="hidden" id="lapso" name="lapso"> 
						<div class="selector-lapso">   
							<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>   
						</div> 
					</div>
				</div>
				<br>
				<!-- Text input-->
				<div class="form-group">
					<div class="col-md-12" style="width: 100%;margin-left: 20px;margin-top:0px">
						<label for="seccion">Seccion</label>      
						<input type="hidden" id="seccion" name="seccion"> 
						<div class="selector-seccion">   
							<select style="width:80%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>    
						</div> 
					</div>
				</div>
				<br>			

			</p>
		
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;">
					
					<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Agregar" style="width:120px"/>
					<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>
		</fieldset>
	</form>
</div>

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


<script>
	$(document).ready(function(){
		hide_loading_message();

                        buscardocente();

                        function buscardocente(){
                        	var parametros = {                        		           
                        		"opcion":"getdocente",
                        		"campos":"docente"
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

                        buscarmateria($('#pensum').val());

                        function buscarmateria(val1){
                        	var parametros = {
                        		"pensum":val1,                       		
                        		"opcion":"getmateria"
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{
                        			$('.selector-cod_mat select').html(response).fadeIn();
                        		}
                        	});
                        }

                        $('.selector-cod_mat select').change(function(){
                        	var v = $(this).val();     
                        	$('#cod_mat').val(v);
                        	$('#electiva').val('');      
                            $('.selector-electiva select').empty();
                            buscarelectiva($('#pensum').val(),$(".selector-cod_mat select").val());						
                        }); 
                  
                        $(".selector-lapso select").change(function() {       
                        	var v = $(this).val(); 
                        	$('#lapso').val(v);                         	
                        });

                        buscarlapso();

                        function buscarlapso(){
                        	var parametros = {                        		
                        		"opcion":"getlapso", 
                        		"campos":"lapso"      
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{             
                        			$('.selector-lapso select').html(response).fadeIn();
                        		}
                        	});
                        }
                        
                        function buscarelectiva(val1,val2){												
                        	var parametros = {
                        		"pensum":val1,
                        		"cod_mat":val2,                       		
                                "opcion":"getelectivas" 
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{
                        			$('.selector-electiva select').html(response).fadeIn();
                        		}
                        	});
                        }

                        buscarseccion($('#pensum').val());

                        function buscarseccion(val1){
                        	var parametros = {
                        	    "pensum":val1,                        		
                        		"opcion":"getnumseccion" 
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{
                        			$('.selector-seccion select').html(response).fadeIn();
                        		}
                        	});
                        }
                        
                        $(".selector-seccion select").change(function() {       
                        	var v = $(this).val(); 
                        	$('#seccion').val(v);                         	
                        });

                        $('.selector-electiva select').change(function(){
                        	var v = $(this).val(); 
                        	$('#electiva').val(v);
                        });
                        
		$('#agregar').click(function(){
			agregar_materia($('#pensum').val(),$('#cod_mat').val(),$('#electiva').val(),$('#lapso').val(),$('#seccion').val(),$('#cedula').val());
		}); 

		function agregar_materia(val1,val2,val3,val4,val5,val6){
			var parametros = {                
				"pensum":val1,				
				"cod_mat":val2,
				"electiva":val3,
				"lapso":val4,
				"seccion":val5,
				"cedula":val6
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "Formulario notas_insertb_x3.php",        
				success: function(response)
				{
					swal(response);					
				}
			}); 
		}

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


                  });
              </script>

          </body>
          </html>


