
<?php
include 'menu.php';
$pensum=$_POST['pensum'];
$cod_doc=$_POST['cod_doc'];
?>
<html>
<head>

</head>
<body>
	<div class="container" id="marco" style="width:45%;height: 250px">
		<form class="form-horizontal" id="effect2" method="post" action="SERVICIOS_SECCION_4.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione la Materia <span class="badge" style="font-weight:bold">03</span></label>
				</div>

				<center><table >
					<tr>
						<td>
							
							<div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
									<label for="cod_doc">Materia</label>
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>" style="width: 0px;">
									<input type="hidden" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>" style="width: 0px;">					
									<input type="hidden" id="cod_mat" name="cod_mat">	
									
								</div>

								<div class="selector-cod_mat" id="sp5">   
									<select style="width:150px;height: 38px;margin-top:8px;margin-left: -185px;position: absolute;" required></select>   
								</div>          
							</div>
						</td>

						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 45px;margin-left: 0px">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente" style="width:120px"/> 
								</div>
							</div>
						</td>


						<td></td> 

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-left:10px;margin-top:45px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>

					<tr>
						<td>
							
							<div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:0px">
									<label for="electiva">Electiva</label>									
									<input type="hidden" id="electiva" name="electiva">	
								</div>

								<div class="selector-electiva">   
									<select style="width:150px;height: 38px;margin-top:30px;margin-left: -185px;position: absolute;"></select>   
								</div>          
							</div>
						</td>
					</tr>


				</table>


				<table id="editable_table" class="table table-bordered table-striped"></table></center>

			</fieldset>
		</form>
	</div>



	<script type="text/javascript">
		$(document).ready(function() {
			
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



});
</script>

</body>
</html>


