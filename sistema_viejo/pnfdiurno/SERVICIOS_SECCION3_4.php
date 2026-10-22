<?php
include 'db.php';
include 'menu.php';

$pensum=$_POST['pensum'];
$lapso=$_POST['lapso'];
$seccion=$_POST['seccion'];
$cedula=$_POST['cedula'];

include "db.php";
$sql="SELECT nombre FROM alumno WHERE cedula='" . $cedula . "'";
$resultado=$conn->query($sql);
if ($resultado->num_rows > 0) {
 while ($fila=$resultado->fetch_assoc()) {
     $nombre=utf8_decode($fila['nombre']);                    
 }
}

?>
<style type="text/css">
	.badge {
		color: #FFFFFF;
		background-color: #0C4783;
	}
	#mensaje{
		width:40%;
		margin:10px auto;
		background:#F0F0F0;  
		border-radius:20px;      
		background: rgba(219, 218, 223,1);
		color:#000000;
		padding: 0;
		border-top-width: 4px;
		border-right-width: 4px;
		border-bottom-width: 4px;
		border-left-width: 4px;
		-webkit-box-shadow: 5px 5px 15px #575853;
		box-shadow: 5px 5px 15px #575853;
		border: 10px solid rgba(12, 71, 131,1);
		border-radius:20px; 
	}

</style>

<div class="container" id="marco" style="width:90%;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione las materias</label>
			</div>

			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">     
					<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>"> 
					<input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">  
					<input type="hidden" id="seccion" name="seccion" value="<?php echo $seccion;?>"> 
					<input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>">           
				</div>              
			</div>

			<!--inicio de la Tabla-->
			<table class="responstable">
				<thead>
					<tr>
						<th width="1%">Pensum</th>
						<th width="2%">Cedula</th>
						<th width="30%">Nombre</th>																	
						<th width="2%">Lapso</th>
						<th width="1%">Seccion</th>												
					</tr>
				</thead>
				<tbody>

					<tr>
						<td><?php echo $pensum;?></td>
						<td><?php echo $cedula;?></td>
						<td><?php echo $nombre;?></td>																	
						<td><?php echo $lapso;?></td>                           
						<td><?php echo $seccion;?></td>
					</tr>       

				</tbody>
			</table>
			<!--Fin de la Tabla-->
			<br />

			<div class="form-group">
				<div class="col-md-12" style="width:150px;margin-left: 20px;">
					<label for="cod_mat">Materia</label>  
					<input id="cod_mat" name="cod_mat" type="hidden" class="form-control">
				</div>

				<div class="selector-cod_mat">   
					<select style="width:90%;height: 38px;margin-top:0px;margin-left: 35px;" required></select>      
				</div>
			</div> 

			<div class="form-group">
				<div class="col-md-12" style="width: 200px;margin-left: 20px;margin-top:0px">
					<label for="electiva">Electiva</label>									
					<input type="hidden" id="electiva" name="electiva">	
				</div>

				<div class="selector-electiva">   
					<select style="width:150px;height: 38px;margin-top:30px;margin-left: -185px;position: absolute;"></select>   
				</div>          
			</div>         
			<br>
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<a type="button" id="agregar" class="btn btn-primary" style=""><span class="glyphicon glyphicon-plus"></span> Agregar</a>
					<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-arrow-left"></span> Salir</a>
				</div>
			</div>

			<table id="editable_table" class="table table-bordered table-striped"></table></center>
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
		$('.selector-electiva select').change(function(){
			var v = $(this).val(); 
			$('#electiva').val(v);
		});
		

		agregar_materia_tabla($("#lapso").val(),$("#seccion").val(),$('#cedula').val(),$('#cod_mat').val());
		
		function agregar_materia_tabla(val1,val2,val3,val4){
			var parametros = {								
				"lapso":val1,
				"seccion":val2,
				"cedula":val3,
				"cod_mat":val4				
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "buscar2c.php",        
				success: function(datos)
				{        
					$('#editable_table').html(datos);
				}
			});  
		}


		

		$('.selector-cod_mat select').change(function(){
			var v = $(this).val(); 
			$('#cod_mat').val(v);
			buscarelectiva($("#pensum").val(),$('#cod_mat').val())           			
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
					// swal(response);
					agregar_materia_tabla($("#lapso").val(),$("#seccion").val(),$('#cedula').val(),$('#cod_mat').val());
				}
			}); 
		}

		

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
	                //Show loading message
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


