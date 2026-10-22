
<?php
include 'menu.php';

$pensum=$_POST['pensum'];
$cod_doc=$_POST['cod_doc'];
$cod_mat=$_POST['cod_mat'];
$electiva=$_POST['electiva'];
$lapso=$_POST['lapso'];
$seccion=$_POST['seccion'];

$carrera=substr($cod_mat, 0, 1);

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

 

<div class="container" id="marco" style="width:800px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione los alumnos</label>
			</div>


			<div class="form-group">
				<div class="col-md-12" style="width:0%;margin-left: 20px;">     
					<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>"> 
					<input type="hidden" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>">
					<input type="hidden" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>">
					<input type="hidden" id="electiva" name="electiva" value="<?php echo $electiva;?>">
					<input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">  
					<input type="hidden" id="seccion" name="seccion" value="<?php echo $seccion;?>">            
				</div>              
			</div>




			<!--inicio de la Tabla-->
			<table class="responstable">
				<thead>
					<tr>
						<th width="1%">Pensum</th>
						<th width="1%">Materia</th>
						<th width="1%">Electiva</th>
						<th width="1%">Docente</th>
						<th width="1%">Lapso</th>
						<th width="1%">Seccion</th> 						
					</tr>
				</thead>
				<tbody>

					<tr>
						<td><?php echo $pensum;?></td>
						<td><?php echo $cod_mat;?></td>
						<td><?php echo $electiva;?></td>
						<td><?php echo $cod_doc;?></td>
						<td><?php echo $lapso;?></td>                           
						<td><?php echo $seccion;?></td>                                                                    
					</tr>       

				</tbody>
			</table>
			<!--Fin de la Tabla-->
			<br />




			<div class="form-group">
				<div class="col-md-12" style="width:150px;margin-left: 20px;">
					<label for="cedula">Alumno</label>  
					<input id="cedula" name="cedula" type="hidden" class="form-control">
				</div>

				<div class="selector-cedula" id="A_1">   
					<select style="width:90%;height: 38px;margin-top:0px;margin-left: 35px;" required></select>      
				</div>
			</div>          


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
		
		$('#cedula').val('');
		$(".selector-cedula select").empty();

		buscaralumno($("#pensum").val(),$("#cod_doc").val(),$("#cod_mat").val(),$("#lapso").val(),$("#seccion").val());

        function agregar_alumno_tabla(val1,val2,val3,val4){
			var parametros = { 				
				"cod_doc":val1,
				"cod_mat":val2,				
				"lapso":val3,
				"seccion":val4				
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "buscar2b.php",        
				success: function(datos)
				{        
					$('#editable_table').html(datos);
			  		}
			});  
		}




		function buscaralumno(val1,val2,val3,val4,val5){
			show_loading_message();
			var parametros = {
				"pensum":val1,
				"cod_doc":val2,
				"cod_mat":val3,				
				"lapso":val4,
				"seccion":val5	
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "getalumno_x.php",        
				success: function(response)
				{
					$(".selector-cedula select").html(response).fadeIn();                    
					hide_loading_message();
				}
			});  
		}

		$('#A_1 select').change(function(){
			var v = $(this).val(); 
			$('#cedula').val(v);  
			
		}); 
          
        agregar_alumno_tabla($('#cod_doc').val(),$('#cod_mat').val(),$('#lapso').val(),$('#seccion').val());
     
		$('#agregar').click(function(){
			agregar_alumno($('#pensum').val(),$('#cod_doc').val(),$('#cod_mat').val(),$('#electiva').val(),$('#lapso').val(),$('#seccion').val(),$('#cedula').val());
			
		}); 


		function agregar_alumno(val1,val2,val3,val4,val5,val6,val7){
			var parametros = {                
				"pensum":val1,
				"cod_doc":val2,
				"cod_mat":val3,
				"electiva":val4,
				"lapso":val5,
				"seccion":val6,
				"cedula":val7
			}
			$.ajax({
				data:parametros,
				type: "POST",
				url: "Formulario notas_insertb_x3.php",        
				success: function(response)

				{
				    //swal(response);
				    agregar_alumno_tabla($('#cod_doc').val(),$('#cod_mat').val(),$('#lapso').val(),$('#seccion').val());
				}
			}); 
		}

		

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


